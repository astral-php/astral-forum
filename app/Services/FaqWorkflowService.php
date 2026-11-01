<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\FaqSubmissionDao;
use App\Dao\ForumDao;
use App\Dao\ForumModerationLogDao;
use App\Models\FaqSubmission;
use App\Support\Slug;
use Core\Auth\Auth;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;
use InvalidArgumentException;
use PDO;
final class FaqWorkflowService
{
    /** @var array<string, list<string>> */
    private const TRANSITIONS = [
        'pending'           => ['under_review', 'rejected'],
        'under_review'      => ['changes_requested', 'rejected', 'approved'],
        'changes_requested' => ['pending', 'under_review', 'rejected'],
        'approved'          => ['published'],
        'published'         => [],
        'rejected'          => ['pending'],
    ];

    public function __construct(
        private PDO $pdo,
        private FaqSubmissionDao $faqDao,
        private ForumDao $forumDao,
        private ForumModerationLogDao $logDao,
        private TopicWriteService $topicWrite,
        private ForumPermissionService $permissions,
    ) {
    }

    public function submit(string $title, string $description, Auth $auth): FaqSubmission
    {
        if (!$auth->check()) {
            throw new AuthorizationException('Connexion requise.');
        }

        $now = date('Y-m-d H:i:s');
        $id = $this->faqDao->insert([
            'user_id'             => (int) $auth->id(),
            'title'               => $title,
            'description'         => $description,
            'status'              => 'pending',
            'reviewed_by'         => null,
            'reviewed_at'         => null,
            'review_note'         => null,
            'published_topic_id'  => null,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        /** @var FaqSubmission */
        return $this->faqDao->findById($id);
    }

    public function withdraw(int $id, Auth $auth): void
    {
        $submission = $this->requireOwned($id, $auth);
        if (!$submission->canWithdraw()) {
            throw new InvalidArgumentException('Cette proposition ne peut plus être retirée.');
        }

        $this->faqDao->delete($submission->id);
    }

    /**
     * Resoumission après rejet (auteur) — repasse en pending.
     */
    public function resubmit(int $id, string $title, string $description, Auth $auth): FaqSubmission
    {
        $submission = $this->requireOwned($id, $auth);
        if (!$submission->canResubmit()) {
            throw new InvalidArgumentException('Seules les propositions rejetées peuvent être resoumises.');
        }

        $now = date('Y-m-d H:i:s');
        $this->faqDao->update($submission->id, [
            'title' => $title,
            'description' => $description,
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_note' => null,
            'updated_at' => $now,
        ]);

        $this->logDao->log(
            (int) $auth->id(),
            'faq.resubmit',
            'faq_submission',
            $submission->id,
            null,
        );

        /** @var FaqSubmission */
        return $this->faqDao->findById($submission->id);
    }

    public function transition(int $id, string $toStatus, Auth $auth, ?string $note = null): FaqSubmission
    {
        if (!$this->permissions->canReviewFaq($auth)) {
            throw new AuthorizationException('Réservé aux administrateurs.');
        }

        $submission = $this->requireSubmission($id);
        $allowed = self::TRANSITIONS[$submission->status] ?? [];

        if (!in_array($toStatus, $allowed, true)) {
            throw new InvalidArgumentException(
                "Transition {$submission->status} → {$toStatus} interdite."
            );
        }

        if ($toStatus === 'published') {
            return $this->publish($submission, $auth);
        }

        $now = date('Y-m-d H:i:s');
        $this->faqDao->update($submission->id, [
            'status'      => $toStatus,
            'reviewed_by' => (int) $auth->id(),
            'reviewed_at' => $now,
            'review_note' => $note,
            'updated_at'  => $now,
        ]);

        $this->logDao->log(
            (int) $auth->id(),
            'faq.' . $toStatus,
            'faq_submission',
            $submission->id,
            $note
        );

        /** @var FaqSubmission */
        return $this->faqDao->findById($submission->id);
    }

    public function publish(FaqSubmission $submission, Auth $auth): FaqSubmission
    {
        if (!$this->permissions->canReviewFaq($auth)) {
            throw new AuthorizationException('Réservé aux administrateurs.');
        }

        if ($submission->published_topic_id !== null || $submission->isPublished()) {
            /** @var FaqSubmission */
            return $this->faqDao->findById($submission->id);
        }

        if ($submission->status !== 'approved') {
            throw new InvalidArgumentException('La proposition doit être approuvée avant publication.');
        }

        $faqForum = $this->forumDao->findFaqForum();
        if ($faqForum === null) {
            // create faq forum on the fly if missing
            $now = date('Y-m-d H:i:s');
            $slug = Slug::unique('faq', fn (string $s): bool => $this->forumDao->slugExists($s));
            $forumId = $this->forumDao->insert([
                'name'        => 'FAQ',
                'slug'        => $slug,
                'description' => 'Questions fréquentes officielles.',
                'type'        => 'faq',
                'status'      => 'active',
                'position'    => 100,
                'created_by'  => (int) $auth->id(),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            /** @var \App\Models\Forum $faqForum */
            $faqForum = $this->forumDao->findById($forumId);
        }

        $this->pdo->beginTransaction();
        try {
            // Re-check inside transaction
            /** @var FaqSubmission $fresh */
            $fresh = $this->faqDao->findById($submission->id);
            if ($fresh->published_topic_id !== null || $fresh->isPublished()) {
                $this->pdo->rollBack();

                return $fresh;
            }

            $topic = $this->topicWrite->createOfficialTopic(
                $faqForum,
                (int) $auth->id(),
                $fresh->title,
                $fresh->description,
            );

            $now = date('Y-m-d H:i:s');
            $this->faqDao->update($fresh->id, [
                'status'             => 'published',
                'published_topic_id' => $topic->id,
                'reviewed_by'        => (int) $auth->id(),
                'reviewed_at'        => $now,
                'updated_at'         => $now,
            ]);

            $this->logDao->log((int) $auth->id(), 'faq.published', 'faq_submission', $fresh->id, null);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        /** @var FaqSubmission */
        return $this->faqDao->findById($submission->id);
    }

    private function requireSubmission(int $id): FaqSubmission
    {
        /** @var FaqSubmission|null $submission */
        $submission = $this->faqDao->findById($id);
        if ($submission === null) {
            throw new NotFoundException("Proposition FAQ #{$id} introuvable.");
        }

        return $submission;
    }

    private function requireOwned(int $id, Auth $auth): FaqSubmission
    {
        $submission = $this->requireSubmission($id);
        if ((int) $auth->id() !== $submission->user_id) {
            throw new NotFoundException("Proposition FAQ #{$id} introuvable.");
        }

        return $submission;
    }
}

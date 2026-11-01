<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Dao\FaqSubmissionDao;
use App\Services\FaqWorkflowService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;
use InvalidArgumentException;

final class FaqReviewController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private Auth $auth,
        private FaqSubmissionDao $faqDao,
        private FaqWorkflowService $workflow,
    ) {
        parent::__construct($view);
    }

    public function index(): Response
    {
        $status = (string) $this->request->query('status', '');
        $page = (int) $this->request->query('page', 1);
        $result = $this->faqDao->paginateForAdmin($page, 20, $status !== '' ? $status : null);

        return $this->render('admin/faq/index', [
            'title'       => 'Admin — File FAQ',
            'submissions' => $result['data'],
            'total'       => $result['total'],
            'pages'       => $result['pages'],
            'current'     => $result['current'],
            'status'      => $status,
        ]);
    }

    public function show(string $id): Response
    {
        /** @var \App\Models\FaqSubmission|null $submission */
        $submission = $this->faqDao->findById((int) $id);
        if ($submission === null) {
            throw new NotFoundException('Proposition introuvable.');
        }

        return $this->render('admin/faq/show', [
            'title'      => 'Examen FAQ',
            'submission' => $submission,
        ]);
    }

    public function takeReview(string $id): Response
    {
        return $this->runTransition((int) $id, 'under_review', 'Proposition prise en charge.');
    }

    public function requestChanges(string $id): Response
    {
        $note = trim((string) ($this->request->post()['review_note'] ?? ''));

        return $this->runTransition((int) $id, 'changes_requested', 'Modifications demandées.', $note ?: null);
    }

    public function reject(string $id): Response
    {
        $note = trim((string) ($this->request->post()['review_note'] ?? ''));

        return $this->runTransition((int) $id, 'rejected', 'Proposition refusée.', $note ?: null);
    }

    public function approve(string $id): Response
    {
        /** @var \App\Models\FaqSubmission|null $submission */
        $submission = $this->faqDao->findById((int) $id);
        if ($submission === null) {
            throw new NotFoundException('FAQ introuvable.');
        }

        try {
            /** @var \App\Models\FaqSubmission $current */
            $current = $submission;
            if ($current->status === 'pending' || $current->status === 'changes_requested') {
                $this->workflow->transition((int) $id, 'under_review', $this->auth);
                /** @var \App\Models\FaqSubmission $current */
                $current = $this->faqDao->findById((int) $id);
            }
            if ($current->status === 'under_review') {
                $this->workflow->transition((int) $id, 'approved', $this->auth);
            }
            /** @var \App\Models\FaqSubmission $fresh */
            $fresh = $this->faqDao->findById((int) $id);
            $this->workflow->publish($fresh, $this->auth);
            $this->session->flash('success', 'FAQ publiée (lecture seule).');
        } catch (InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/faq/proposals/' . $id);
    }

    public function publish(string $id): Response
    {
        /** @var \App\Models\FaqSubmission|null $submission */
        $submission = $this->faqDao->findById((int) $id);
        if ($submission === null) {
            throw new NotFoundException('Proposition introuvable.');
        }

        try {
            $this->workflow->publish($submission, $this->auth);
            $this->session->flash('success', 'FAQ publiée.');
        } catch (InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/faq/proposals/' . $id);
    }

    private function runTransition(int $id, string $to, string $okMessage, ?string $note = null): Response
    {
        try {
            $this->workflow->transition($id, $to, $this->auth, $note);
            $this->session->flash('success', $okMessage);
        } catch (InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        return $this->redirect('/admin/faq/proposals/' . $id);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Dao\ForumDao;
use App\Dao\TopicDao;
use App\Services\ModerationService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;

final class ModerationController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private Auth $auth,
        private ModerationService $moderation,
        private TopicDao $topicDao,
        private ForumDao $forumDao,
    ) {
        parent::__construct($view);
    }

    public function lock(string $id): Response
    {
        $topic = $this->moderation->lockTopic((int) $id, $this->auth, $this->reason());
        $this->session->flash('success', 'Sujet verrouillé.');

        return $this->redirectToTopic($topic->id);
    }

    public function unlock(string $id): Response
    {
        $topic = $this->moderation->unlockTopic((int) $id, $this->auth, $this->reason());
        $this->session->flash('success', 'Sujet déverrouillé.');

        return $this->redirectToTopic($topic->id);
    }

    public function pin(string $id): Response
    {
        $topic = $this->moderation->pinTopic((int) $id, $this->auth, $this->reason());
        $this->session->flash('success', 'Sujet épinglé.');

        return $this->redirectToTopic($topic->id);
    }

    public function unpin(string $id): Response
    {
        $topic = $this->moderation->unpinTopic((int) $id, $this->auth, $this->reason());
        $this->session->flash('success', 'Sujet désépinglé.');

        return $this->redirectToTopic($topic->id);
    }

    public function hidePost(string $id): Response
    {
        $post = $this->moderation->hidePost((int) $id, $this->auth, $this->reason());
        $this->session->flash('success', 'Message masqué.');

        return $this->redirectToTopic($post->topic_id);
    }

    private function reason(): ?string
    {
        $reason = trim((string) ($this->request->post()['reason'] ?? ''));

        return $reason !== '' ? $reason : null;
    }

    private function redirectToTopic(int $topicId): Response
    {
        /** @var \App\Models\Topic|null $topic */
        $topic = $this->topicDao->findById($topicId);
        if ($topic === null) {
            throw new NotFoundException('Sujet introuvable.');
        }

        /** @var \App\Models\Forum|null $forum */
        $forum = $this->forumDao->findById($topic->forum_id);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        return $this->redirect(
            '/forums/' . rawurlencode($forum->slug)
            . '/topics/' . rawurlencode($topic->slug)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\ForumDao;
use App\Dao\TopicDao;
use App\Services\ActionThrottler;
use App\Services\ForumPermissionService;
use App\Services\PostWriteService;
use App\Services\TopicWriteService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;

final class PostController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private Auth $auth,
        private ForumDao $forumDao,
        private TopicDao $topicDao,
        private TopicWriteService $topicWrite,
        private PostWriteService $postWrite,
        private ForumPermissionService $permissions,
        private ActionThrottler $throttler,
    ) {
        parent::__construct($view);
    }

    public function store(string $id): Response
    {
        $topicId = (int) $id;
        /** @var \App\Models\Topic|null $topic */
        $topic = $this->topicDao->findById($topicId);
        if ($topic === null || !$topic->isPublished()) {
            throw new NotFoundException('Sujet introuvable.');
        }

        /** @var \App\Models\Forum|null $forum */
        $forum = $this->forumDao->findById($topic->forum_id);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        $topicUrl = '/forums/' . rawurlencode($forum->slug)
            . '/topics/' . rawurlencode($topic->slug);

        $userId = (int) $this->auth->id();
        if ($this->throttler->tooManyAttempts('reply', $userId)) {
            $wait = $this->throttler->availableIn('reply', $userId);
            $this->session->flash(
                'error',
                sprintf('Trop de réponses. Réessayez dans %d s.', $wait)
            );

            return $this->redirect($topicUrl);
        }

        $post = $this->request->post();
        $v = $this->validate($post, [
            'content' => 'required|min:3|max:20000',
        ]);

        if ($v->fails()) {
            $this->session->flash('error', 'Message invalide.');

            return $this->redirect($topicUrl);
        }

        try {
            $this->topicWrite->reply($topicId, trim((string) $post['content']), $this->auth);
            $this->throttler->hit('reply', $userId);
        } catch (AuthorizationException $e) {
            $this->session->flash('error', $e->getMessage());

            return $this->redirect($topicUrl);
        }

        $this->session->flash('success', 'Réponse publiée.');

        return $this->redirect($topicUrl);
    }

    public function editForm(string $id): Response
    {
        $ctx = $this->postWrite->requireContext((int) $id);
        $post = $ctx['post'];
        $topic = $ctx['topic'];
        $forum = $ctx['forum'];

        if (!$this->permissions->canEditPost($post, $this->auth)) {
            throw new AuthorizationException('Vous ne pouvez pas modifier ce message.');
        }

        return $this->render('posts/edit', [
            'title' => 'Modifier le message',
            'forum' => $forum,
            'topic' => $topic,
            'post' => $post,
            'errors' => [],
            'old' => ['content' => $post->content],
        ]);
    }

    public function update(string $id): Response
    {
        $ctx = $this->postWrite->requireContext((int) $id);
        $topic = $ctx['topic'];
        $forum = $ctx['forum'];
        $topicUrl = '/forums/' . rawurlencode($forum->slug)
            . '/topics/' . rawurlencode($topic->slug);

        $input = $this->request->post();
        $v = $this->validate($input, [
            'content' => 'required|min:3|max:20000',
        ]);

        if ($v->fails()) {
            return $this->render('posts/edit', [
                'title' => 'Modifier le message',
                'forum' => $forum,
                'topic' => $topic,
                'post' => $ctx['post'],
                'errors' => $v->errors(),
                'old' => $input,
            ]);
        }

        try {
            $this->postWrite->update((int) $id, trim((string) $input['content']), $this->auth);
        } catch (AuthorizationException $e) {
            $this->session->flash('error', $e->getMessage());

            return $this->redirect($topicUrl);
        }

        $this->session->flash('success', 'Message mis à jour.');

        return $this->redirect($topicUrl);
    }

    public function destroy(string $id): Response
    {
        $ctx = $this->postWrite->requireContext((int) $id);
        $topicUrl = '/forums/' . rawurlencode($ctx['forum']->slug)
            . '/topics/' . rawurlencode($ctx['topic']->slug);

        try {
            $this->postWrite->softDelete((int) $id, $this->auth);
            $this->session->flash('success', 'Message supprimé.');
        } catch (AuthorizationException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        return $this->redirect($topicUrl);
    }
}

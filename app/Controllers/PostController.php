<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\ForumDao;
use App\Dao\TopicDao;
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

        $post = $this->request->post();
        $v = $this->validate($post, [
            'content' => 'required|min:3|max:20000',
        ]);

        if ($v->fails()) {
            $this->session->flash('error', 'Message invalide.');

            return $this->redirect(
                '/forums/' . rawurlencode($forum->slug)
                . '/topics/' . rawurlencode($topic->slug)
            );
        }

        try {
            $this->topicWrite->reply($topicId, trim((string) $post['content']), $this->auth);
        } catch (AuthorizationException $e) {
            $this->session->flash('error', $e->getMessage());

            return $this->redirect(
                '/forums/' . rawurlencode($forum->slug)
                . '/topics/' . rawurlencode($topic->slug)
            );
        }

        $this->session->flash('success', 'Réponse publiée.');

        return $this->redirect(
            '/forums/' . rawurlencode($forum->slug)
            . '/topics/' . rawurlencode($topic->slug)
        );
    }
}

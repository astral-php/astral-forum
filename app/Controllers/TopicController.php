<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\ForumDao;
use App\Dao\PostDao;
use App\Dao\TopicDao;
use App\Dao\UserDao;
use App\Services\ForumPermissionService;
use App\Services\TopicWriteService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;

final class TopicController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private Auth $auth,
        private ForumDao $forumDao,
        private TopicDao $topicDao,
        private PostDao $postDao,
        private UserDao $userDao,
        private ForumPermissionService $permissions,
        private TopicWriteService $topicWrite,
    ) {
        parent::__construct($view);
    }

    public function show(string $slug, string $topicSlug): Response
    {
        $forum = $this->forumDao->findBySlug($slug);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        $topic = $this->topicDao->findPublishedByForumAndSlug($forum->id, $topicSlug);
        if ($topic === null) {
            throw new NotFoundException('Sujet introuvable.');
        }

        $posts = $this->postDao->findPublishedByTopic($topic->id);
        $authors = [];
        foreach ($posts as $post) {
            if (!isset($authors[$post->user_id])) {
                $authors[$post->user_id] = $this->userDao->findById($post->user_id);
            }
        }

        return $this->render('topics/show', [
            'title'     => $topic->title,
            'forum'     => $forum,
            'topic'     => $topic,
            'posts'     => $posts,
            'authors'   => $authors,
            'canReply'  => $this->permissions->canReply($forum, $topic, $this->auth),
            'canModerate' => $this->permissions->canModerate($this->auth),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function createForm(string $slug): Response
    {
        $forum = $this->forumDao->findActiveBySlug($slug);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        if (!$this->permissions->canCreateTopic($forum, $this->auth)) {
            throw new AuthorizationException('Création de sujet non autorisée.');
        }

        return $this->render('topics/create', [
            'title'  => 'Nouveau sujet',
            'forum'  => $forum,
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function store(string $slug): Response
    {
        $forum = $this->forumDao->findActiveBySlug($slug);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        $post = $this->request->post();
        $v = $this->validate($post, [
            'title'   => 'required|min:3|max:240',
            'content' => 'required|min:3|max:20000',
        ]);

        if ($v->fails()) {
            return $this->render('topics/create', [
                'title'  => 'Nouveau sujet',
                'forum'  => $forum,
                'errors' => $v->errors(),
                'old'    => $post,
            ]);
        }

        $result = $this->topicWrite->createTopic(
            $forum->slug,
            trim((string) $post['title']),
            trim((string) $post['content']),
            $this->auth,
        );

        $this->session->flash('success', 'Sujet publié.');

        return $this->redirect(
            '/forums/' . rawurlencode($result['forum']->slug)
            . '/topics/' . rawurlencode($result['topic']->slug)
        );
    }
}

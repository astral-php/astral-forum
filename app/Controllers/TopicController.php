<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\ForumDao;
use App\Dao\PostDao;
use App\Dao\TopicDao;
use App\Dao\TopicSubscriptionDao;
use App\Dao\UserDao;
use App\Services\ActionThrottler;
use App\Services\ForumPermissionService;
use App\Services\TopicWriteService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;
use Core\Http\RedirectResponse;
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
        private ActionThrottler $throttler,
        private TopicSubscriptionDao $subscriptions,
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

        $this->trackView($topic->id);
        /** @var \App\Models\Topic $topic */
        $topic = $this->topicDao->findById($topic->id);

        $posts = $this->postDao->findPublishedByTopic($topic->id);
        $authors = [];
        $canEdit = [];
        $canSoftDelete = [];
        foreach ($posts as $post) {
            if (!isset($authors[$post->user_id])) {
                $authors[$post->user_id] = $this->userDao->findById($post->user_id);
            }
            $canEdit[$post->id] = $this->permissions->canEditPost($post, $this->auth);
            $canSoftDelete[$post->id] = $this->permissions->canSoftDeletePost($post, $this->auth)
                && $topic->first_post_id !== $post->id;
        }

        $subscribed = false;
        if ($this->auth->check()) {
            $subscribed = $this->subscriptions->isSubscribed($topic->id, (int) $this->auth->id());
        }

        return $this->render('topics/show', [
            'title' => $topic->title,
            'forum' => $forum,
            'topic' => $topic,
            'posts' => $posts,
            'authors' => $authors,
            'canReply' => $this->permissions->canReply($forum, $topic, $this->auth),
            'canModerate' => $this->permissions->canModerate($this->auth),
            'canEdit' => $canEdit,
            'canSoftDelete' => $canSoftDelete,
            'subscribed' => $subscribed,
            'errors' => [],
            'old' => [],
        ]);
    }

    public function createForm(string $slug): Response
    {
        $forum = $this->forumDao->findActiveBySlug($slug);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        if ($forum->isFaq()) {
            return RedirectResponse::to('/faq/proposals/create');
        }

        if (!$this->permissions->canCreateTopic($forum, $this->auth)) {
            throw new AuthorizationException('Création de sujet non autorisée.');
        }

        return $this->render('topics/create', [
            'title' => 'Nouveau sujet',
            'forum' => $forum,
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store(string $slug): Response
    {
        $forum = $this->forumDao->findActiveBySlug($slug);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        if ($forum->isFaq()) {
            return RedirectResponse::to('/faq/proposals/create');
        }

        $userId = (int) $this->auth->id();
        if ($this->throttler->tooManyAttempts('topic', $userId)) {
            $wait = $this->throttler->availableIn('topic', $userId);

            return $this->render('topics/create', [
                'title' => 'Nouveau sujet',
                'forum' => $forum,
                'errors' => ['title' => [
                    sprintf('Trop de sujets créés. Réessayez dans %d s.', $wait),
                ]],
                'old' => $this->request->post(),
            ]);
        }

        $post = $this->request->post();
        $v = $this->validate($post, [
            'title' => 'required|min:3|max:240',
            'content' => 'required|min:3|max:20000',
        ]);

        if ($v->fails()) {
            return $this->render('topics/create', [
                'title' => 'Nouveau sujet',
                'forum' => $forum,
                'errors' => $v->errors(),
                'old' => $post,
            ]);
        }

        try {
            $result = $this->topicWrite->createTopic(
                $forum->slug,
                trim((string) $post['title']),
                trim((string) $post['content']),
                $this->auth,
            );
            $this->throttler->hit('topic', $userId);
        } catch (AuthorizationException $e) {
            return $this->render('topics/create', [
                'title' => 'Nouveau sujet',
                'forum' => $forum,
                'errors' => ['title' => [$e->getMessage()]],
                'old' => $post,
            ]);
        }

        $this->session->flash('success', 'Sujet publié.');

        return $this->redirect(
            '/forums/' . rawurlencode($result['forum']->slug)
            . '/topics/' . rawurlencode($result['topic']->slug)
        );
    }

    public function subscribe(string $id): Response
    {
        $topic = $this->requirePublishedTopic((int) $id);
        $this->subscriptions->subscribe($topic->id, (int) $this->auth->id());
        $this->session->flash('success', 'Abonnement activé — e-mail à chaque réponse.');

        return $this->redirectToTopic($topic);
    }

    public function unsubscribe(string $id): Response
    {
        $topic = $this->requirePublishedTopic((int) $id);
        $this->subscriptions->unsubscribe($topic->id, (int) $this->auth->id());
        $this->session->flash('success', 'Abonnement retiré.');

        return $this->redirectToTopic($topic);
    }

    private function trackView(int $topicId): void
    {
        /** @var list<int> $seen */
        $seen = $this->session->get('viewed_topics', []);
        if (!is_array($seen)) {
            $seen = [];
        }
        if (in_array($topicId, $seen, true)) {
            return;
        }

        $this->topicDao->incrementViewCount($topicId);
        $seen[] = $topicId;
        $this->session->set('viewed_topics', $seen);
    }

    private function requirePublishedTopic(int $topicId): \App\Models\Topic
    {
        /** @var \App\Models\Topic|null $topic */
        $topic = $this->topicDao->findById($topicId);
        if ($topic === null || !$topic->isPublished()) {
            throw new NotFoundException('Sujet introuvable.');
        }

        return $topic;
    }

    private function redirectToTopic(\App\Models\Topic $topic): Response
    {
        /** @var \App\Models\Forum|null $forum */
        $forum = $this->forumDao->findById($topic->forum_id);
        if ($forum === null) {
            return $this->redirect('/forums');
        }

        return $this->redirect(
            '/forums/' . rawurlencode($forum->slug)
            . '/topics/' . rawurlencode($topic->slug)
        );
    }
}

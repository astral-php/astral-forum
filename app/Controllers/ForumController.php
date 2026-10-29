<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\ForumDao;
use App\Dao\TopicDao;
use App\Services\ForumPermissionService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\View;

final class ForumController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private ForumDao $forumDao,
        private TopicDao $topicDao,
        private ForumPermissionService $permissions,
        private Auth $auth,
    ) {
        parent::__construct($view);
    }

    public function index(): Response
    {
        // Cache fichier astral-cache refuse les objets ; listes via DAO (cache array/ids plus tard).
        return $this->render('forums/index', [
            'title'  => 'Forums',
            'forums' => $this->forumDao->findPublicActive(),
        ]);
    }

    public function show(string $slug): Response
    {
        $forum = $this->forumDao->findBySlug($slug);
        if ($forum === null || (!$forum->isActive() && !$this->permissions->canManageForums($this->auth))) {
            throw new NotFoundException('Forum introuvable.');
        }

        $page = (int) $this->request->query('page', 1);
        $result = $this->topicDao->paginatePublishedByForum($forum->id, $page, 20);

        return $this->render('forums/show', [
            'title'            => $forum->name,
            'forum'            => $forum,
            'topics'           => $result['data'],
            'total'            => $result['total'],
            'pages'            => $result['pages'],
            'current'          => $result['current'],
            'canCreateTopic'   => $this->permissions->canCreateTopic($forum, $this->auth),
        ]);
    }
}

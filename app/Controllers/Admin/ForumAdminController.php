<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Dao\ForumDao;
use App\Support\Slug;
use Astral\Cache\CacheRepository;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;
use InvalidArgumentException;

final class ForumAdminController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private Auth $auth,
        private ForumDao $forumDao,
        private CacheRepository $cache,
    ) {
        parent::__construct($view);
    }

    public function index(): Response
    {
        return $this->render('admin/forums/index', [
            'title'  => 'Admin — Forums',
            'forums' => $this->forumDao->findAllOrdered(),
        ]);
    }

    public function store(): Response
    {
        $post = $this->request->post();
        $v = $this->validate($post, [
            'name' => 'required|min:2|max:160',
            'type' => 'required',
        ]);

        $type = (string) ($post['type'] ?? '');
        if (!in_array($type, ['specialized', 'faq'], true)) {
            $v = $this->validate($post, ['name' => 'required']);
            $this->session->flash('error', 'Type de forum invalide (specialized ou faq).');

            return $this->redirect('/admin/forums');
        }

        if ($v->fails()) {
            $this->session->flash('error', 'Données invalides.');

            return $this->redirect('/admin/forums');
        }

        if ($type === 'faq' && $this->forumDao->findFaqForum() !== null) {
            $this->session->flash('error', 'Un forum FAQ actif existe déjà.');

            return $this->redirect('/admin/forums');
        }

        $now = date('Y-m-d H:i:s');
        $name = trim((string) $post['name']);
        $slug = Slug::unique($name, fn (string $s): bool => $this->forumDao->slugExists($s));

        $this->forumDao->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim((string) ($post['description'] ?? '')) ?: null,
            'type'        => $type,
            'status'      => 'active',
            'position'    => (int) ($post['position'] ?? 10),
            'created_by'  => (int) $this->auth->id(),
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->cache->forget('forums.public_active');
        $this->session->flash('success', 'Forum créé.');

        return $this->redirect('/admin/forums');
    }

    public function update(string $id): Response
    {
        /** @var \App\Models\Forum|null $forum */
        $forum = $this->forumDao->findById((int) $id);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        $post = $this->request->post();
        $name = trim((string) ($post['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Nom requis.');
        }

        $this->forumDao->update($forum->id, [
            'name'        => $name,
            'description' => trim((string) ($post['description'] ?? '')) ?: null,
            'position'    => (int) ($post['position'] ?? $forum->position),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->cache->forget('forums.public_active');
        $this->session->flash('success', 'Forum mis à jour.');

        return $this->redirect('/admin/forums');
    }

    public function archive(string $id): Response
    {
        /** @var \App\Models\Forum|null $forum */
        $forum = $this->forumDao->findById((int) $id);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        if ($forum->isGeneral()) {
            $this->session->flash('error', 'Le forum général ne peut pas être archivé.');

            return $this->redirect('/admin/forums');
        }

        $this->forumDao->update($forum->id, [
            'status'     => 'archived',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->cache->forget('forums.public_active');
        $this->session->flash('success', 'Forum archivé.');

        return $this->redirect('/admin/forums');
    }
}

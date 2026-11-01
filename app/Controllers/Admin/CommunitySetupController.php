<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Dao\ForumDao;
use App\Services\CommunitySetupService;
use App\Support\CommunitySettings;
use Controller\AbstractController;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;

final class CommunitySetupController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private CommunitySettings $settings,
        private CommunitySetupService $setup,
        private ForumDao $forumDao,
    ) {
        parent::__construct($view);
    }

    public function form(): Response
    {
        $general = $this->forumDao->findGeneral();

        return $this->render('admin/setup/form', [
            'title' => $this->settings->isConfigured() ? 'Communauté' : 'Configurer la communauté',
            'configured' => $this->settings->isConfigured(),
            'old' => [
                'name' => $this->settings->name(),
                'tagline' => $this->settings->isConfigured() ? $this->settings->tagline() : '',
                'general_description' => $general?->description ?: $this->settings->generalDescription(),
            ],
        ]);
    }

    public function store(): Response
    {
        $post = $this->request->post();
        $v = $this->validate($post, [
            'name' => 'required|min:3|max:160',
            'tagline' => 'required|min:8|max:280',
            'general_description' => 'required|min:8|max:400',
        ]);

        if ($v->fails()) {
            return $this->render('admin/setup/form', [
                'title' => 'Configurer la communauté',
                'configured' => $this->settings->isConfigured(),
                'errors' => $v->errors(),
                'old' => $post,
            ]);
        }

        $this->setup->apply(
            (string) $post['name'],
            (string) $post['tagline'],
            (string) $post['general_description'],
        );

        $this->session->flash('success', 'Identité de la communauté enregistrée.');

        return $this->redirect('/admin/forums');
    }
}

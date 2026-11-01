<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\UserDao;
use App\Services\ForumCatalogService;
use Controller\AbstractController;
use Core\Http\Response;
use Core\View;

/**
 * Accueil forum — Nova Maroon Light (mix maquette + catalogue).
 */
final class HomeController extends AbstractController
{
    public function __construct(
        View $view,
        private UserDao $userDao,
        private ForumCatalogService $catalog,
        private string $version = '0.1.2',
        private string $githubUrl = 'https://github.com/astral-php/astral-forum',
        private string $websiteUrl = 'https://astral-php.dev',
    ) {
        parent::__construct($view);
    }

    public function index(): Response
    {
        return $this->render('home/index', [
            'title' => 'Astral Forum',
            'version' => $this->version,
            'hasUsers' => $this->userDao->count() > 0,
            'githubUrl' => $this->githubUrl,
            'websiteUrl' => $this->websiteUrl,
            'forums' => $this->catalog->publicActive(),
        ]);
    }
}

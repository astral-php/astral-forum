<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\UserDao;
use Controller\AbstractController;
use Core\Http\Response;
use Core\View;

/**
 * Accueil du starter — invitation à créer le premier admin + liens écosystème.
 */
final class HomeController extends AbstractController
{
    public function __construct(
        View $view,
        private UserDao $userDao,
        private string $version = '0.1.1',
        private string $githubUrl = 'https://github.com/astral-php',
        private string $websiteUrl = 'https://github.com/astral-php',
    ) {
        parent::__construct($view);
    }

    public function index(): Response
    {
        return $this->render('home/index', [
            'title'      => 'Astral Starter',
            'version'    => $this->version,
            'hasUsers'   => $this->userDao->count() > 0,
            'githubUrl'  => $this->githubUrl,
            'websiteUrl' => $this->websiteUrl,
        ]);
    }
}

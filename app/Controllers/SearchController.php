<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SearchService;
use Controller\AbstractController;
use Core\Http\Response;
use Core\Request;
use Core\View;

final class SearchController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private SearchService $search,
    ) {
        parent::__construct($view);
    }

    public function index(): Response
    {
        $q = trim((string) $this->request->query('q', ''));
        $page = max(1, (int) $this->request->query('page', 1));
        $result = $this->search->search($q, $page);

        return $this->render('search/index', [
            'title' => $q !== '' ? ('Recherche : ' . $q) : 'Recherche',
            'q' => $result['query'],
            'results' => $result['data'],
            'total' => $result['total'],
            'current' => $result['current'],
            'pages' => $result['pages'],
            'baseUrl' => '/search?q=' . rawurlencode($result['query']),
        ]);
    }
}

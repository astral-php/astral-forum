<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dao\FaqSubmissionDao;
use App\Services\FaqWorkflowService;
use Controller\AbstractController;
use Core\Auth\Auth;
use Core\Exception\NotFoundException;
use Core\Http\Response;
use Core\Request;
use Core\Session;
use Core\View;

final class FaqProposalController extends AbstractController
{
    public function __construct(
        View $view,
        private Request $request,
        private Session $session,
        private Auth $auth,
        private FaqSubmissionDao $faqDao,
        private FaqWorkflowService $workflow,
    ) {
        parent::__construct($view);
    }

    public function createForm(): Response
    {
        return $this->render('faq/create', [
            'title'  => 'Proposer une FAQ',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function store(): Response
    {
        $post = $this->request->post();
        $v = $this->validate($post, [
            'title'       => 'required|min:5|max:240',
            'description' => 'required|min:10|max:20000',
        ]);

        if ($v->fails()) {
            return $this->render('faq/create', [
                'title'  => 'Proposer une FAQ',
                'errors' => $v->errors(),
                'old'    => $post,
            ]);
        }

        $submission = $this->workflow->submit(
            trim((string) $post['title']),
            trim((string) $post['description']),
            $this->auth,
        );

        $this->session->flash('success', 'Proposition envoyée.');

        return $this->redirect('/account/faq-proposals/' . $submission->id);
    }

    public function index(): Response
    {
        $items = $this->faqDao->findByUser((int) $this->auth->id());

        return $this->render('faq/account_index', [
            'title'       => 'Mes propositions FAQ',
            'submissions' => $items,
        ]);
    }

    public function show(string $id): Response
    {
        /** @var \App\Models\FaqSubmission|null $submission */
        $submission = $this->faqDao->findById((int) $id);
        if ($submission === null || $submission->user_id !== (int) $this->auth->id()) {
            throw new NotFoundException('Proposition introuvable.');
        }

        return $this->render('faq/account_show', [
            'title'      => 'Proposition FAQ',
            'submission' => $submission,
        ]);
    }

    public function withdraw(string $id): Response
    {
        $this->workflow->withdraw((int) $id, $this->auth);
        $this->session->flash('success', 'Proposition retirée.');

        return $this->redirect('/account/faq-proposals');
    }
}

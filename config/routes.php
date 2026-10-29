<?php

declare(strict_types=1);

use App\Controllers\Admin\FaqReviewController;
use App\Controllers\Admin\ForumAdminController;
use App\Controllers\Admin\ModerationController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\FaqProposalController;
use App\Controllers\ForumController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Controllers\ProfileController;
use App\Controllers\TopicController;
use App\Controllers\UserController;
use Core\Auth\Middleware\AdminMiddleware;
use Core\Auth\Middleware\AuthMiddleware;
use Core\Auth\Middleware\GuestMiddleware;
use Core\Router;

return function (Router $router): void {

    $router->get('/', HomeController::class, 'index');

    $router->get('/login',     AuthController::class, 'loginForm')->middleware(GuestMiddleware::class);
    $router->post('/login',    AuthController::class, 'login')->middleware(GuestMiddleware::class);
    $router->post('/logout',   AuthController::class, 'logout');

    $router->get('/register',  AuthController::class, 'registerForm')->middleware(GuestMiddleware::class);
    $router->post('/register', AuthController::class, 'register')->middleware(GuestMiddleware::class);

    $router->get('/verify-pending', AuthController::class, 'verifyPending');
    $router->get('/verify-email',   AuthController::class, 'verifyEmail');

    $router->get('/forgot-password',  AuthController::class, 'forgotPasswordForm')->middleware(GuestMiddleware::class);
    $router->post('/forgot-password', AuthController::class, 'forgotPassword')->middleware(GuestMiddleware::class);
    $router->get('/reset-password',   AuthController::class, 'resetPasswordForm')->middleware(GuestMiddleware::class);
    $router->post('/reset-password',  AuthController::class, 'resetPassword')->middleware(GuestMiddleware::class);

    // Forums — routes spécifiques avant :topicSlug
    $router->get('/forums', ForumController::class, 'index');
    $router->get('/forums/:slug/topics/create', TopicController::class, 'createForm')
        ->middleware(AuthMiddleware::class);
    $router->post('/forums/:slug/topics', TopicController::class, 'store')
        ->middleware(AuthMiddleware::class);
    $router->get('/forums/:slug', ForumController::class, 'show');
    $router->get('/forums/:slug/topics/:topicSlug', TopicController::class, 'show');

    $router->group('', function (Router $r): void {
        $r->get('/profile',           ProfileController::class, 'editForm');
        $r->post('/profile',          ProfileController::class, 'update');
        $r->post('/profile/password', ProfileController::class, 'updatePassword');

        $r->post('/topics/:id/posts', PostController::class, 'store');

        $r->get('/faq/proposals/create', FaqProposalController::class, 'createForm');
        $r->post('/faq/proposals', FaqProposalController::class, 'store');
        $r->get('/account/faq-proposals', FaqProposalController::class, 'index');
        $r->get('/account/faq-proposals/:id', FaqProposalController::class, 'show');
        $r->post('/account/faq-proposals/:id/withdraw', FaqProposalController::class, 'withdraw');
    }, [AuthMiddleware::class]);

    $router->group('', function (Router $r): void {
        $r->get('/users',     UserController::class, 'index');
        $r->get('/users/:id', UserController::class, 'show');
    }, [AuthMiddleware::class]);

    $router->group('', function (Router $r): void {
        $r->get('/users/create',      UserController::class, 'create');
        $r->post('/users',            UserController::class, 'store');
        $r->post('/users/:id/delete', UserController::class, 'destroy');
    }, [AdminMiddleware::class]);

    $router->group('/admin', function (Router $r): void {
        $r->get('/users',           AdminUserController::class, 'index');
        $r->post('/users/:id/role', AdminUserController::class, 'updateRole');

        $r->get('/forums', ForumAdminController::class, 'index');
        $r->post('/forums', ForumAdminController::class, 'store');
        $r->post('/forums/:id', ForumAdminController::class, 'update');
        $r->post('/forums/:id/archive', ForumAdminController::class, 'archive');

        $r->get('/faq/proposals', FaqReviewController::class, 'index');
        $r->get('/faq/proposals/:id', FaqReviewController::class, 'show');
        $r->post('/faq/proposals/:id/review', FaqReviewController::class, 'takeReview');
        $r->post('/faq/proposals/:id/request-changes', FaqReviewController::class, 'requestChanges');
        $r->post('/faq/proposals/:id/reject', FaqReviewController::class, 'reject');
        $r->post('/faq/proposals/:id/approve', FaqReviewController::class, 'approve');
        $r->post('/faq/proposals/:id/publish', FaqReviewController::class, 'publish');

        $r->post('/topics/:id/lock', ModerationController::class, 'lock');
        $r->post('/topics/:id/unlock', ModerationController::class, 'unlock');
        $r->post('/topics/:id/pin', ModerationController::class, 'pin');
        $r->post('/topics/:id/unpin', ModerationController::class, 'unpin');
        $r->post('/posts/:id/hide', ModerationController::class, 'hidePost');
    }, [AdminMiddleware::class]);
};

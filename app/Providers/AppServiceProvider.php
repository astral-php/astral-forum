<?php

declare(strict_types=1);

namespace App\Providers;

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
use App\Dao\FaqSubmissionDao;
use App\Dao\ForumDao;
use App\Dao\ForumModerationLogDao;
use App\Dao\PostDao;
use App\Dao\TopicDao;
use App\Dao\UserDao;
use App\Events\RoleChanged;
use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Listeners\LogRoleChange;
use App\Listeners\LogUserActivity;
use App\Listeners\SendWelcomeEmail;
use App\Services\FaqWorkflowService;
use App\Services\ForumPermissionService;
use App\Services\ModerationService;
use App\Services\TopicWriteService;
use Astral\Cache\CacheRepository;
use Core\Auth\Auth;
use Core\Container;
use Core\Events\EventDispatcher;
use Core\Mailer\Mailer;
use Core\Request;
use Core\ServiceProviderInterface;
use Core\Session;
use Core\View;
use PDO;

/**
 * Services applicatifs Astral Forum.
 */
final class AppServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container, array $appConfig, array $dbConfig): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $container->make(EventDispatcher::class);

        $dispatcher->listen(UserRegistered::class, SendWelcomeEmail::class);
        $dispatcher->listen(UserRegistered::class, LogUserActivity::class);
        $dispatcher->listen(UserLoggedIn::class,   LogUserActivity::class);
        $dispatcher->listen(RoleChanged::class,    LogRoleChange::class);

        $container->singleton(UserDao::class, function (Container $c) use ($dbConfig): UserDao {
            $dao = new UserDao($c->make(PDO::class));

            if (($dbConfig['driver'] ?? 'sqlite') === 'sqlite') {
                $dao->createTableIfNotExists();
            }

            return $dao;
        });

        $container->singleton(ForumDao::class, fn (Container $c) => new ForumDao($c->make(PDO::class)));
        $container->singleton(TopicDao::class, fn (Container $c) => new TopicDao($c->make(PDO::class)));
        $container->singleton(PostDao::class, fn (Container $c) => new PostDao($c->make(PDO::class)));
        $container->singleton(FaqSubmissionDao::class, fn (Container $c) => new FaqSubmissionDao($c->make(PDO::class)));
        $container->singleton(ForumModerationLogDao::class, fn (Container $c) => new ForumModerationLogDao($c->make(PDO::class)));

        $container->singleton(ForumPermissionService::class, fn () => new ForumPermissionService());

        $container->singleton(TopicWriteService::class, fn (Container $c) => new TopicWriteService(
            $c->make(PDO::class),
            $c->make(ForumDao::class),
            $c->make(TopicDao::class),
            $c->make(PostDao::class),
            $c->make(ForumPermissionService::class),
        ));

        $container->singleton(FaqWorkflowService::class, fn (Container $c) => new FaqWorkflowService(
            $c->make(PDO::class),
            $c->make(FaqSubmissionDao::class),
            $c->make(ForumDao::class),
            $c->make(ForumModerationLogDao::class),
            $c->make(TopicWriteService::class),
            $c->make(ForumPermissionService::class),
        ));

        $container->singleton(ModerationService::class, fn (Container $c) => new ModerationService(
            $c->make(TopicDao::class),
            $c->make(PostDao::class),
            $c->make(ForumModerationLogDao::class),
            $c->make(ForumPermissionService::class),
        ));

        $container->singleton(Mailer::class, function () use ($appConfig): Mailer {
            /** @var array<string, mixed> $mail */
            $mail = $appConfig['mail'] ?? [];

            return new Mailer(
                driver:     (string) ($mail['driver']     ?? 'mail'),
                host:       (string) ($mail['host']       ?? 'localhost'),
                port:       (int)    ($mail['port']       ?? 25),
                username:   (string) ($mail['username']   ?? ''),
                password:   (string) ($mail['password']   ?? ''),
                encryption: (string) ($mail['encryption'] ?? ''),
                from:       (string) ($mail['from']       ?? 'noreply@localhost'),
                fromName:   (string) ($mail['from_name']  ?? 'Astral Forum'),
            );
        });

        $container->bind(HomeController::class, fn (Container $c) => new HomeController(
            view:        $c->make(View::class),
            userDao:     $c->make(UserDao::class),
            version:     (string) ($appConfig['version'] ?? '0.1.0'),
            githubUrl:   (string) ($appConfig['github_url'] ?? 'https://github.com/astral-php'),
            websiteUrl:  (string) ($appConfig['website_url'] ?? 'https://github.com/astral-php'),
        ));

        $container->bind(UserController::class, fn (Container $c) => new UserController(
            view:    $c->make(View::class),
            request: $c->make(Request::class),
            userDao: $c->make(UserDao::class),
            session: $c->make(Session::class),
            auth:    $c->make(Auth::class),
        ));

        $container->bind(ProfileController::class, fn (Container $c) => new ProfileController(
            view:    $c->make(View::class),
            request: $c->make(Request::class),
            auth:    $c->make(Auth::class),
            userDao: $c->make(UserDao::class),
            session: $c->make(Session::class),
        ));

        $container->bind(AdminUserController::class, fn (Container $c) => new AdminUserController(
            view:       $c->make(View::class),
            request:    $c->make(Request::class),
            userDao:    $c->make(UserDao::class),
            session:    $c->make(Session::class),
            auth:       $c->make(Auth::class),
            dispatcher: $c->make(EventDispatcher::class),
        ));

        $container->bind(AuthController::class, fn (Container $c) => new AuthController(
            view:             $c->make(View::class),
            request:          $c->make(Request::class),
            auth:             $c->make(Auth::class),
            userDao:          $c->make(UserDao::class),
            session:          $c->make(Session::class),
            mailer:           $c->make(Mailer::class),
            dispatcher:       $c->make(EventDispatcher::class),
            registrationMode: (string) ($appConfig['auth_registration'] ?? 'direct'),
            appBaseUrl:       (string) ($appConfig['base_url'] ?? ''),
        ));

        $container->bind(ForumController::class, fn (Container $c) => new ForumController(
            view:        $c->make(View::class),
            request:     $c->make(Request::class),
            forumDao:    $c->make(ForumDao::class),
            topicDao:    $c->make(TopicDao::class),
            permissions: $c->make(ForumPermissionService::class),
            auth:        $c->make(Auth::class),
        ));

        $container->bind(TopicController::class, fn (Container $c) => new TopicController(
            view:        $c->make(View::class),
            request:     $c->make(Request::class),
            session:     $c->make(Session::class),
            auth:        $c->make(Auth::class),
            forumDao:    $c->make(ForumDao::class),
            topicDao:    $c->make(TopicDao::class),
            postDao:     $c->make(PostDao::class),
            userDao:     $c->make(UserDao::class),
            permissions: $c->make(ForumPermissionService::class),
            topicWrite:  $c->make(TopicWriteService::class),
        ));

        $container->bind(PostController::class, fn (Container $c) => new PostController(
            view:       $c->make(View::class),
            request:    $c->make(Request::class),
            session:    $c->make(Session::class),
            auth:       $c->make(Auth::class),
            forumDao:   $c->make(ForumDao::class),
            topicDao:   $c->make(TopicDao::class),
            topicWrite: $c->make(TopicWriteService::class),
        ));

        $container->bind(FaqProposalController::class, fn (Container $c) => new FaqProposalController(
            view:     $c->make(View::class),
            request:  $c->make(Request::class),
            session:  $c->make(Session::class),
            auth:     $c->make(Auth::class),
            faqDao:   $c->make(FaqSubmissionDao::class),
            workflow: $c->make(FaqWorkflowService::class),
        ));

        $container->bind(ForumAdminController::class, fn (Container $c) => new ForumAdminController(
            view:     $c->make(View::class),
            request:  $c->make(Request::class),
            session:  $c->make(Session::class),
            auth:     $c->make(Auth::class),
            forumDao: $c->make(ForumDao::class),
            cache:    $c->make(CacheRepository::class),
        ));

        $container->bind(FaqReviewController::class, fn (Container $c) => new FaqReviewController(
            view:     $c->make(View::class),
            request:  $c->make(Request::class),
            session:  $c->make(Session::class),
            auth:     $c->make(Auth::class),
            faqDao:   $c->make(FaqSubmissionDao::class),
            workflow: $c->make(FaqWorkflowService::class),
        ));

        $container->bind(ModerationController::class, fn (Container $c) => new ModerationController(
            view:       $c->make(View::class),
            request:    $c->make(Request::class),
            session:    $c->make(Session::class),
            auth:       $c->make(Auth::class),
            moderation: $c->make(ModerationService::class),
            topicDao:   $c->make(TopicDao::class),
            forumDao:   $c->make(ForumDao::class),
        ));
    }
}

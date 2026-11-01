<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Auth\ForumRoles;
use Core\Auth\Auth;
use Core\Auth\Role;
use Core\Exception\AuthorizationException;
use Core\Http\RedirectResponse;
use Core\Middleware\MiddlewareInterface;
use Core\Request;

/**
 * Admin ou modérateur (lock / pin / hide).
 */
final class ModeratorMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly Auth $auth)
    {
    }

    #[\Override]
    public function handle(Request $request, callable $next): mixed
    {
        if (!$this->auth->check()) {
            return RedirectResponse::to('/login');
        }

        if (!$this->auth->can(Role::Admin, ForumRoles::MODERATOR)) {
            throw new AuthorizationException('Accès réservé aux modérateurs.');
        }

        return $next();
    }
}

<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Dao\UserDao;
use Astral\AuthExtra\RememberMe\RememberMeManager;
use Core\Auth\Auth;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;
use Core\Request;

final class RememberMeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Auth $auth,
        private RememberMeManager $remember,
        private UserDao $userDao,
    ) {
    }

    #[\Override]
    public function handle(Request $request, callable $next): mixed
    {
        $cookieName = $this->remember->cookieName();
        $raw = isset($_COOKIE[$cookieName]) ? (string) $_COOKIE[$cookieName] : null;

        if ($this->auth->guest() && $raw !== null && $raw !== '') {
            $token = $this->remember->authenticateFromCookie($raw);
            if ($token !== null) {
                $user = $this->userDao->findById((int) $token['user_id']);
                if ($user !== null) {
                    $this->auth->login($user);
                    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
                    header(
                        'Set-Cookie: ' . $this->remember->buildSetCookieHeader(
                            $token['cookie_value'],
                            $token['expires_at'],
                            $secure
                        ),
                        false
                    );
                }
            }
        }

        $result = $next();

        return $result instanceof Response ? $result : $result;
    }
}

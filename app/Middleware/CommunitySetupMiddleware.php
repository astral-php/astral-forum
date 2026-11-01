<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Support\CommunitySettings;
use Core\Http\RedirectResponse;
use Core\Middleware\MiddlewareInterface;
use Core\Request;

final class CommunitySetupMiddleware implements MiddlewareInterface
{
    public function __construct(private CommunitySettings $settings)
    {
    }

    #[\Override]
    public function handle(Request $request, callable $next): mixed
    {
        $path = (string) (parse_url($request->uri, PHP_URL_PATH) ?: '/');
        if (str_starts_with($path, '/admin/setup')) {
            return $next();
        }

        if ($this->settings->isConfigured()) {
            return $next();
        }

        return RedirectResponse::to('/admin/setup');
    }
}

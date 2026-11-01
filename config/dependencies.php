<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Astral\AuthExtra\AuthExtraServiceProvider;
use Astral\Cache\CacheServiceProvider;
use Astral\Form\FormServiceProvider;
use Core\Providers\DatabaseServiceProvider;
use Core\Providers\FrameworkServiceProvider;

return [
    FrameworkServiceProvider::class,
    DatabaseServiceProvider::class,
    CacheServiceProvider::class,
    FormServiceProvider::class,
    AuthExtraServiceProvider::class,
    AppServiceProvider::class,
];

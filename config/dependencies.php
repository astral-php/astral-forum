<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Astral\Cache\CacheServiceProvider;
use Astral\Form\FormServiceProvider;
use Core\Providers\DatabaseServiceProvider;
use Core\Providers\FrameworkServiceProvider;

return [
    FrameworkServiceProvider::class,
    DatabaseServiceProvider::class,
    CacheServiceProvider::class,
    FormServiceProvider::class,
    AppServiceProvider::class,
];

<?php

declare(strict_types=1);

/**
 * @return array<string, mixed>
 */
return [
    'name'     => $_ENV['APP_NAME']     ?? 'Astral Forum',
    'version'  => $_ENV['APP_VERSION']  ?? '0.4.0',
    'env'      => $_ENV['APP_ENV']      ?? 'production',
    'debug'    => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'charset'  => $_ENV['APP_CHARSET']  ?? 'UTF-8',
    'base_url' => $_ENV['APP_BASE_URL'] ?? '',

    'auth_registration' => $_ENV['AUTH_REGISTRATION'] ?? 'direct',

    'github_url'  => $_ENV['ASTRAL_GITHUB_URL']  ?? 'https://github.com/astral-php',
    'website_url' => $_ENV['ASTRAL_WEBSITE_URL'] ?? 'https://github.com/astral-php',

    // Nova maroon — famille UI Astral
    'primary'      => $_ENV['FORUM_PRIMARY']      ?? '#9f1239',
    'primary_soft' => $_ENV['FORUM_PRIMARY_SOFT'] ?? '#fce7ef',

    'forum' => [
        'edit_window_minutes' => (int) ($_ENV['FORUM_EDIT_WINDOW_MINUTES'] ?? 30),
        'auto_subscribe' => filter_var($_ENV['FORUM_AUTO_SUBSCRIBE'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'notify_on_reply' => filter_var($_ENV['FORUM_NOTIFY_ON_REPLY'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'throttle' => [
            'topic' => [
                'max' => (int) ($_ENV['FORUM_THROTTLE_TOPIC_MAX'] ?? 5),
                'decay' => (int) ($_ENV['FORUM_THROTTLE_TOPIC_DECAY'] ?? 600),
            ],
            'reply' => [
                'max' => (int) ($_ENV['FORUM_THROTTLE_REPLY_MAX'] ?? 10),
                'decay' => (int) ($_ENV['FORUM_THROTTLE_REPLY_DECAY'] ?? 300),
            ],
            'faq' => [
                'max' => (int) ($_ENV['FORUM_THROTTLE_FAQ_MAX'] ?? 3),
                'decay' => (int) ($_ENV['FORUM_THROTTLE_FAQ_DECAY'] ?? 900),
            ],
        ],
    ],

    'auth_extra' => [
        'max_attempts' => (int) ($_ENV['AUTH_EXTRA_MAX_ATTEMPTS'] ?? 5),
        'decay_seconds' => (int) ($_ENV['AUTH_EXTRA_DECAY_SECONDS'] ?? 900),
        'remember_days' => (int) ($_ENV['AUTH_EXTRA_REMEMBER_DAYS'] ?? 30),
        'log_to_database' => filter_var($_ENV['AUTH_EXTRA_LOG_TO_DB'] ?? false, FILTER_VALIDATE_BOOLEAN),
    ],

    'mail' => [
        'driver'      => $_ENV['MAIL_DRIVER']      ?? 'mail',
        'host'        => $_ENV['MAIL_HOST']         ?? 'localhost',
        'port'        => (int) ($_ENV['MAIL_PORT']  ?? 25),
        'username'    => $_ENV['MAIL_USERNAME']     ?? '',
        'password'    => $_ENV['MAIL_PASSWORD']     ?? '',
        'encryption'  => $_ENV['MAIL_ENCRYPTION']   ?? '',
        'from'        => $_ENV['MAIL_FROM']         ?? 'noreply@localhost',
        'from_name'   => $_ENV['MAIL_FROM_NAME']    ?? 'Astral Forum',
    ],
];

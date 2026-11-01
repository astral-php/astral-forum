<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\TopicSubscriptionDao;
use App\Dao\UserDao;
use App\Models\Forum;
use App\Models\Topic;
use Core\Mailer\Mailer;

/**
 * Notifications e-mail sync sur nouvelle réponse (abonnés).
 */
final class TopicNotificationService
{
    public function __construct(
        private TopicSubscriptionDao $subscriptions,
        private UserDao $userDao,
        private Mailer $mailer,
        private string $baseUrl = '',
        private bool $enabled = true,
    ) {
    }

    public function notifyNewReply(Topic $topic, Forum $forum, int $authorId, string $snippet): void
    {
        if (!$this->enabled) {
            return;
        }

        $ids = $this->subscriptions->subscriberIds($topic->id, $authorId);
        if ($ids === []) {
            return;
        }

        $url = rtrim($this->baseUrl, '/')
            . '/forums/' . rawurlencode($forum->slug)
            . '/topics/' . rawurlencode($topic->slug);

        $safeTitle = htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8');
        $safeSnippet = htmlspecialchars(mb_substr(trim($snippet), 0, 280), ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        foreach ($ids as $userId) {
            $user = $this->userDao->findById($userId);
            if ($user === null || empty($user->email)) {
                continue;
            }

            $html = <<<HTML
<p>Nouvelle réponse dans <strong>{$safeTitle}</strong> ({$forum->name}).</p>
<blockquote style="border-left:3px solid #9f1239;padding-left:0.75rem;color:#555">{$safeSnippet}</blockquote>
<p><a href="{$safeUrl}">Voir le sujet</a></p>
HTML;

            try {
                $this->mailer->send(
                    (string) $user->email,
                    'Nouvelle réponse — ' . $topic->title,
                    $html,
                );
            } catch (\Throwable) {
                // Ne bloque pas la publication.
            }
        }
    }
}

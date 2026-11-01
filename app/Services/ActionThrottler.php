<?php

declare(strict_types=1);

namespace App\Services;

use Astral\AuthExtra\Throttle\ThrottleStoreInterface;

/**
 * Throttle générique (sujet / réponse / FAQ) via le store auth-extra.
 */
final class ActionThrottler
{
    /**
     * @param array<string, array{max?: int, decay?: int}> $limits
     */
    public function __construct(
        private ThrottleStoreInterface $store,
        private array $limits = [],
        private string $prefix = 'forum_throttle',
    ) {
    }

    public function key(string $action, int $userId): string
    {
        return $this->prefix . ':' . $action . ':' . $userId;
    }

    public function tooManyAttempts(string $action, int $userId): bool
    {
        return $this->attempts($action, $userId) >= $this->maxAttempts($action);
    }

    public function hit(string $action, int $userId): int
    {
        $key = $this->key($action, $userId);
        $payload = $this->read($key);
        $attempts = $payload['attempts'] + 1;
        $decay = $this->decaySeconds($action);

        $this->store->set($key, [
            'attempts' => $attempts,
            'expires_at' => time() + $decay,
        ], $decay);

        return $attempts;
    }

    public function availableIn(string $action, int $userId): int
    {
        if (!$this->tooManyAttempts($action, $userId)) {
            return 0;
        }

        return max(0, $this->read($this->key($action, $userId))['expires_at'] - time());
    }

    public function attempts(string $action, int $userId): int
    {
        return $this->read($this->key($action, $userId))['attempts'];
    }

    public function maxAttempts(string $action): int
    {
        return (int) ($this->limits[$action]['max'] ?? 5);
    }

    public function decaySeconds(string $action): int
    {
        return (int) ($this->limits[$action]['decay'] ?? 600);
    }

    /** @return array{attempts: int, expires_at: int} */
    private function read(string $key): array
    {
        $data = $this->store->get($key);
        if (!is_array($data)) {
            return ['attempts' => 0, 'expires_at' => time() + 600];
        }

        return [
            'attempts' => (int) ($data['attempts'] ?? 0),
            'expires_at' => (int) ($data['expires_at'] ?? time() + 600),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ActionThrottler;
use Astral\AuthExtra\Throttle\ArrayThrottleStore;
use PHPUnit\Framework\TestCase;

final class ActionThrottlerTest extends TestCase
{
    public function test_blocks_after_max_attempts(): void
    {
        $throttler = new ActionThrottler(
            store: new ArrayThrottleStore(),
            limits: ['reply' => ['max' => 3, 'decay' => 60]],
        );

        $this->assertFalse($throttler->tooManyAttempts('reply', 7));
        $throttler->hit('reply', 7);
        $throttler->hit('reply', 7);
        $throttler->hit('reply', 7);
        $this->assertTrue($throttler->tooManyAttempts('reply', 7));
        $this->assertGreaterThan(0, $throttler->availableIn('reply', 7));
    }

    public function test_isolates_actions_and_users(): void
    {
        $throttler = new ActionThrottler(
            store: new ArrayThrottleStore(),
            limits: [
                'topic' => ['max' => 1, 'decay' => 60],
                'faq' => ['max' => 5, 'decay' => 60],
            ],
        );

        $throttler->hit('topic', 1);
        $this->assertTrue($throttler->tooManyAttempts('topic', 1));
        $this->assertFalse($throttler->tooManyAttempts('faq', 1));
        $this->assertFalse($throttler->tooManyAttempts('topic', 2));
    }
}

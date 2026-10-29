<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Slug;
use PHPUnit\Framework\TestCase;

final class SlugTest extends TestCase
{
    public function test_from_basic(): void
    {
        $this->assertSame('hello-world', Slug::from('Hello World!'));
    }

    public function test_unique_suffix(): void
    {
        $existing = ['hello'];
        $slug = Slug::unique('Hello', static fn (string $s): bool => in_array($s, $existing, true));
        $this->assertSame('hello-2', $slug);
    }
}

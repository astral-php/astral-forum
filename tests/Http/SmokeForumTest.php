<?php

declare(strict_types=1);

namespace Tests\Http;

/** Smokes HTTP forum — lecture publique + garde auth. */
final class SmokeForumTest extends HttpSmokeTestCase
{
    public function testForumsIndexReturns200WithGeneral(): void
    {
        $response = $this->get('/forums');
        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Forum général', $response->getContent());
    }

    public function testGeneralForumShowReturns200(): void
    {
        $response = $this->get('/forums/general');
        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Forum général', $response->getContent());
    }

    public function testUnknownForumReturns404(): void
    {
        $this->expectException(\Core\Exception\NotFoundException::class);
        $this->get('/forums/does-not-exist');
    }

    public function testCreateTopicWithoutAuthRedirects(): void
    {
        $response = $this->get('/forums/general/topics/create');
        $this->assertSame(302, $response->getStatus());
    }

    public function testAdminForumsWithoutAuthRedirects(): void
    {
        $response = $this->get('/admin/forums');
        $this->assertSame(302, $response->getStatus());
    }

    public function testHomeMentionsForum(): void
    {
        $response = $this->get('/');
        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Astral Forum', $response->getContent());
    }
}

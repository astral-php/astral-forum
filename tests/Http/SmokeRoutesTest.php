<?php

declare(strict_types=1);

namespace Tests\Http;

/** Smokes HTTP auth / pages de base. */
final class SmokeRoutesTest extends HttpSmokeTestCase
{
    public function testHomeReturns200(): void
    {
        $response = $this->get('/');
        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Astral', $response->getContent());
    }

    public function testLoginFormReturns200(): void
    {
        $response = $this->get('/login');
        $this->assertSame(200, $response->getStatus());
    }

    public function testRegisterFormReturns200(): void
    {
        $response = $this->get('/register');
        $this->assertSame(200, $response->getStatus());
    }

    public function testProfileWithoutAuthRedirectsToLogin(): void
    {
        $response = $this->get('/profile');
        $this->assertSame(302, $response->getStatus());
    }

    public function testAdminWithoutAuthRedirects(): void
    {
        $response = $this->get('/admin/users');
        $this->assertSame(302, $response->getStatus());
    }
}

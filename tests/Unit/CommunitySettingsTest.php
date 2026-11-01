<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\CommunitySettings;
use PHPUnit\Framework\TestCase;

final class CommunitySettingsTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->path = sys_get_temp_dir() . '/astral-community-' . uniqid('', true) . '.json';
    }

    protected function tearDown(): void
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }
        parent::tearDown();
    }

    public function test_defaults_until_saved(): void
    {
        $settings = new CommunitySettings($this->path, 'Astral Forum');
        $this->assertFalse($settings->isConfigured());
        $this->assertSame('Astral Forum', $settings->name());
    }

    public function test_save_marks_configured(): void
    {
        $settings = new CommunitySettings($this->path);
        $settings->save(
            'La communauté des apiculteurs du Jura',
            'Échanges autour des ruches et de la saison.',
            'Annonces et discussions du rucher.',
        );

        $reloaded = new CommunitySettings($this->path);
        $this->assertTrue($reloaded->isConfigured());
        $this->assertSame('La communauté des apiculteurs du Jura', $reloaded->name());
        $this->assertStringContainsString('apiculteurs', $reloaded->faqDescription());
    }
}

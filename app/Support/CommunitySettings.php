<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Identité communauté (fichier JSON, pas de .env).
 */
final class CommunitySettings
{
    public const GENERAL_NAME = 'Forum général';
    public const FAQ_NAME = 'FAQ';

    public function __construct(
        private string $path,
        private string $defaultName = 'Astral Forum',
    ) {
    }

    public function isConfigured(): bool
    {
        return (bool) ($this->read()['configured'] ?? false);
    }

    public function name(): string
    {
        $name = trim((string) ($this->read()['name'] ?? ''));

        return $name !== '' ? $name : $this->defaultName;
    }

    public function tagline(): string
    {
        $tagline = trim((string) ($this->read()['tagline'] ?? ''));

        return $tagline !== ''
            ? $tagline
            : 'Retrouvez les discussions, posez vos questions et partagez vos expériences.';
    }

    public function generalDescription(): string
    {
        $description = trim((string) ($this->read()['general_description'] ?? ''));

        return $description !== ''
            ? $description
            : 'Discussions générales de la communauté.';
    }

    public function faqDescription(): string
    {
        return 'Questions fréquentes — ' . $this->name() . '.';
    }

    /**
     * @return array{name: string, tagline: string, general_description: string, configured: bool}
     */
    public function save(string $name, string $tagline, string $generalDescription): array
    {
        $payload = [
            'configured' => true,
            'name' => trim($name),
            'tagline' => trim($tagline),
            'general_description' => trim($generalDescription),
        ];

        $dir = dirname($this->path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Impossible de créer le dossier de configuration communauté.');
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false || file_put_contents($this->path, $json) === false) {
            throw new \RuntimeException('Impossible d’enregistrer la configuration communauté.');
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function read(): array
    {
        if (!is_file($this->path)) {
            return [];
        }

        $raw = file_get_contents($this->path);
        if ($raw === false || $raw === '') {
            return [];
        }

        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }
}

<?php

declare(strict_types=1);

namespace App\Support;

final class Slug
{
    public static function from(string $text, int $max = 200): string
    {
        $text = trim(mb_strtolower($text, 'UTF-8'));
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');

        if ($text === '') {
            $text = 'item';
        }

        if (mb_strlen($text) > $max) {
            $text = rtrim(mb_substr($text, 0, $max), '-');
        }

        return $text;
    }

    public static function unique(string $base, callable $exists): string
    {
        $slug = self::from($base);
        $candidate = $slug;
        $i = 2;

        while ($exists($candidate)) {
            $candidate = $slug . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}

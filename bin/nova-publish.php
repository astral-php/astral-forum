<?php

declare(strict_types=1);

/**
 * Publie les assets Nova Design vers public/assets/nova/.
 *
 * Sources (vendor) :
 *   astral-nova-css/dist + src/javascript
 *   astral-nova-icons/dist
 *
 * Usage :
 *   php bin/nova-publish.php
 *   composer nova:publish
 *
 * Après `composer update` des packages Nova → republier.
 */

$base = dirname(__DIR__);
$vendor = $base . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'astral-php';
$dest = $base . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'nova';

$cssPkg = $vendor . DIRECTORY_SEPARATOR . 'astral-nova-css';
$iconsPkg = $vendor . DIRECTORY_SEPARATOR . 'astral-nova-icons';

$errors = [];

if (!is_dir($cssPkg)) {
    $errors[] = 'Package manquant : astral-php/astral-nova-css';
}
if (!is_dir($iconsPkg)) {
    $errors[] = 'Package manquant : astral-php/astral-nova-icons';
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

$ensureDir = static function (string $dir): void {
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException("Impossible de créer : {$dir}");
    }
};

$copyFile = static function (string $from, string $to) use ($ensureDir): void {
    if (!is_file($from)) {
        throw new RuntimeException("Source absente : {$from}");
    }
    $ensureDir(dirname($to));
    if (!copy($from, $to)) {
        throw new RuntimeException("Copie échouée : {$from} → {$to}");
    }
};

$copied = [];

try {
    $ensureDir($dest);
    $ensureDir($dest . DIRECTORY_SEPARATOR . 'javascript');
    $ensureDir($dest . DIRECTORY_SEPARATOR . 'icons');
    $ensureDir($dest . DIRECTORY_SEPARATOR . 'illustrations');

    // CSS builds
    foreach (['nova.css', 'nova.min.css'] as $file) {
        $from = $cssPkg . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . $file;
        $to = $dest . DIRECTORY_SEPARATOR . $file;
        $copyFile($from, $to);
        $copied[] = "public/assets/nova/{$file}";
    }

    // JS modules (nova.js applicatif est conservé s'il existe)
    $jsSrc = $cssPkg . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'javascript';
    foreach (['index.js', 'theme.js', 'components.js'] as $file) {
        $from = $jsSrc . DIRECTORY_SEPARATOR . $file;
        $to = $dest . DIRECTORY_SEPARATOR . 'javascript' . DIRECTORY_SEPARATOR . $file;
        $copyFile($from, $to);
        $copied[] = "public/assets/nova/javascript/{$file}";
    }

    $novaJs = $dest . DIRECTORY_SEPARATOR . 'nova.js';
    if (!is_file($novaJs)) {
        $stub = <<<'JS'
import { theme, createInteractions } from './javascript/index.js';
createInteractions();
document.querySelector('[data-nova-theme-toggle]')?.addEventListener('click', () => {
  theme.setMode(theme.getMode() === 'dark' ? 'light' : 'dark');
});
JS;
        if (file_put_contents($novaJs, $stub) === false) {
            throw new RuntimeException('Écriture impossible : nova.js');
        }
        $copied[] = 'public/assets/nova/nova.js (créé)';
    }

    // Icons
    $iconsDist = $iconsPkg . DIRECTORY_SEPARATOR . 'dist';
    $copyFile(
        $iconsDist . DIRECTORY_SEPARATOR . 'nova-icons.css',
        $dest . DIRECTORY_SEPARATOR . 'nova-icons.css',
    );
    $copied[] = 'public/assets/nova/nova-icons.css';

    foreach (['icons.svg', 'icons.json'] as $file) {
        $copyFile(
            $iconsDist . DIRECTORY_SEPARATOR . $file,
            $dest . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $file,
        );
        $copied[] = "public/assets/nova/icons/{$file}";
    }

    $hero = $iconsDist . DIRECTORY_SEPARATOR . 'hero-default.svg';
    if (is_file($hero)) {
        $copyFile($hero, $dest . DIRECTORY_SEPARATOR . 'illustrations' . DIRECTORY_SEPARATOR . 'hero-default.svg');
        $copied[] = 'public/assets/nova/illustrations/hero-default.svg';
    }
} catch (Throwable $e) {
    fwrite(STDERR, '✗ ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

echo "✓ Assets Nova publiés (" . count($copied) . " fichiers)" . PHP_EOL;
foreach ($copied as $line) {
    echo "  · {$line}" . PHP_EOL;
}
echo PHP_EOL . "Rappel : après composer update des packages Nova → composer nova:publish" . PHP_EOL;
exit(0);

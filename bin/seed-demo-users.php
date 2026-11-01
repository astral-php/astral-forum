<?php

declare(strict_types=1);

/**
 * Upsert comptes démo pour vérif visuelle (local).
 * Mot de passe commun : DemoForum-2026!
 *
 *   php bin/seed-demo-users.php
 */

$basePath = dirname(__DIR__);
$dbPath = $basePath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'forum.sqlite';

if (!is_file($dbPath)) {
    fwrite(STDERR, "DB introuvable : {$dbPath}\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$password = 'DemoForum-2026!';
$hash = password_hash($password, PASSWORD_BCRYPT);
$now = date('Y-m-d H:i:s');

$accounts = [
    [
        'name' => 'Demo Admin',
        'email' => 'demo-admin@astral-forum.local',
        'role' => 'admin',
    ],
    [
        'name' => 'Demo Modérateur',
        'email' => 'demo-moderator@astral-forum.local',
        'role' => 'moderator',
    ],
    [
        'name' => 'Demo User',
        'email' => 'demo-user@astral-forum.local',
        'role' => 'user',
    ],
];

$find = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$update = $pdo->prepare(
    'UPDATE users SET name = :name, password = :password, role = :role, email_verified_at = :verified WHERE id = :id'
);
$insert = $pdo->prepare(
    'INSERT INTO users (name, email, password, role, email_verified_at, created_at)
     VALUES (:name, :email, :password, :role, :verified, :created_at)'
);

foreach ($accounts as $account) {
    $find->execute([':email' => $account['email']]);
    $id = $find->fetchColumn();

    if ($id !== false) {
        $update->execute([
            ':name' => $account['name'],
            ':password' => $hash,
            ':role' => $account['role'],
            ':verified' => $now,
            ':id' => (int) $id,
        ]);
        echo "Updated {$account['email']} (id {$id}, {$account['role']})\n";
        continue;
    }

    $insert->execute([
        ':name' => $account['name'],
        ':email' => $account['email'],
        ':password' => $hash,
        ':role' => $account['role'],
        ':verified' => $now,
        ':created_at' => $now,
    ]);
    echo "Created {$account['email']} (id " . $pdo->lastInsertId() . ", {$account['role']})\n";
}

echo "\nLogin :\n";
foreach ($accounts as $account) {
    $label = str_pad(ucfirst($account['role']), 9);
    echo "  {$label} {$account['email']} / {$password}\n";
}

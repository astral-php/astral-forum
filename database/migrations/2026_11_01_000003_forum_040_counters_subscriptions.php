<?php

declare(strict_types=1);

use Database\Migration\Migration;

/**
 * 0.4.0 — compteurs sujets, réponses FAQ configurables, abonnements.
 */
class Forum040CountersSubscriptions extends Migration
{
    public function up(PDO $pdo): void
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $this->upSqlite($pdo);
        } else {
            $this->upMysql($pdo);
        }

        $this->backfillReplyCounts($pdo);
        $this->defaultsAllowReplies($pdo);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec('DROP TABLE IF EXISTS topic_subscriptions');
        // Colonnes laissées en place (SQLite ALTER DROP limité).
    }

    private function upSqlite(PDO $pdo): void
    {
        $this->addColumnIfMissing($pdo, 'forums', 'allow_member_replies', 'INTEGER NOT NULL DEFAULT 1');
        $this->addColumnIfMissing($pdo, 'topics', 'reply_count', 'INTEGER NOT NULL DEFAULT 0');
        $this->addColumnIfMissing($pdo, 'topics', 'view_count', 'INTEGER NOT NULL DEFAULT 0');

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS topic_subscriptions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                topic_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                UNIQUE (topic_id, user_id),
                FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
            )
        SQL);
        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_topic_subs_user ON topic_subscriptions(user_id)');
    }

    private function upMysql(PDO $pdo): void
    {
        $this->addColumnIfMissingMysql($pdo, 'forums', 'allow_member_replies', 'TINYINT(1) NOT NULL DEFAULT 1');
        $this->addColumnIfMissingMysql($pdo, 'topics', 'reply_count', 'INT UNSIGNED NOT NULL DEFAULT 0');
        $this->addColumnIfMissingMysql($pdo, 'topics', 'view_count', 'INT UNSIGNED NOT NULL DEFAULT 0');

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS topic_subscriptions (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                topic_id INT UNSIGNED NOT NULL,
                user_id INT UNSIGNED NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY ux_topic_user (topic_id, user_id),
                KEY ix_topic_subs_user (user_id),
                CONSTRAINT fk_subs_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        SQL);
    }

    private function backfillReplyCounts(PDO $pdo): void
    {
        $pdo->exec(<<<'SQL'
            UPDATE topics
            SET reply_count = (
                SELECT COUNT(*) FROM posts
                WHERE posts.topic_id = topics.id
                  AND posts.status = 'published'
                  AND (topics.first_post_id IS NULL OR posts.id != topics.first_post_id)
            )
        SQL);
    }

    private function defaultsAllowReplies(PDO $pdo): void
    {
        $pdo->exec("UPDATE forums SET allow_member_replies = 0 WHERE type = 'faq'");
        $pdo->exec("UPDATE forums SET allow_member_replies = 1 WHERE type != 'faq'");
    }

    private function addColumnIfMissing(PDO $pdo, string $table, string $column, string $definition): void
    {
        $stmt = $pdo->query("PRAGMA table_info({$table})");
        $cols = $stmt !== false ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        foreach ($cols as $col) {
            if (($col['name'] ?? '') === $column) {
                return;
            }
        }
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }

    private function addColumnIfMissingMysql(PDO $pdo, string $table, string $column, string $definition): void
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c'
        );
        $stmt->execute([':t' => $table, ':c' => $column]);
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}

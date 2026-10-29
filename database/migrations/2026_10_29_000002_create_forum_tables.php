<?php

declare(strict_types=1);

use Database\Migration\Migration;

/**
 * Tables forum : forums, topics, posts, faq_submissions, forum_moderation_logs.
 * Seed idempotent du forum général.
 */
class CreateForumTables extends Migration
{
    public function up(PDO $pdo): void
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $this->upSqlite($pdo);
        } else {
            $this->upMysql($pdo);
        }

        $this->seedGeneralForum($pdo);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec('DROP TABLE IF EXISTS forum_moderation_logs');
        $pdo->exec('DROP TABLE IF EXISTS faq_submissions');
        $pdo->exec('DROP TABLE IF EXISTS posts');
        $pdo->exec('DROP TABLE IF EXISTS topics');
        $pdo->exec('DROP TABLE IF EXISTS forums');
    }

    private function upSqlite(PDO $pdo): void
    {
        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS forums (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(160) NOT NULL,
                slug VARCHAR(180) NOT NULL UNIQUE,
                description TEXT NULL,
                type VARCHAR(24) NOT NULL,
                status VARCHAR(24) NOT NULL DEFAULT 'active',
                position INTEGER NOT NULL DEFAULT 0,
                created_by INTEGER NULL,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        SQL);

        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS ux_forums_single_general ON forums(type) WHERE type = \'general\'');
        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_forums_status_position ON forums(status, position)');

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS topics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                forum_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                first_post_id INTEGER NULL,
                title VARCHAR(240) NOT NULL,
                slug VARCHAR(260) NOT NULL,
                status VARCHAR(24) NOT NULL DEFAULT 'published',
                is_pinned INTEGER NOT NULL DEFAULT 0,
                is_locked INTEGER NOT NULL DEFAULT 0,
                last_post_at TEXT NULL,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (forum_id) REFERENCES forums(id) ON DELETE RESTRICT,
                UNIQUE (forum_id, slug)
            )
        SQL);

        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_topics_forum_status_created ON topics(forum_id, status, created_at, id)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_topics_forum_pin_activity ON topics(forum_id, is_pinned, last_post_at)');

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                topic_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                content TEXT NOT NULL,
                status VARCHAR(24) NOT NULL DEFAULT 'published',
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                edited_at TEXT NULL,
                FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE RESTRICT
            )
        SQL);

        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_posts_topic_status_created ON posts(topic_id, status, created_at, id)');

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS faq_submissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title VARCHAR(240) NOT NULL,
                description TEXT NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'pending',
                reviewed_by INTEGER NULL,
                reviewed_at TEXT NULL,
                review_note TEXT NULL,
                published_topic_id INTEGER NULL UNIQUE,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (published_topic_id) REFERENCES topics(id) ON DELETE RESTRICT
            )
        SQL);

        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_faq_submissions_status_created ON faq_submissions(status, created_at)');

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS forum_moderation_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                actor_user_id INTEGER NOT NULL,
                action VARCHAR(64) NOT NULL,
                target_type VARCHAR(32) NOT NULL,
                target_id INTEGER NOT NULL,
                reason TEXT NULL,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        SQL);

        $pdo->exec('CREATE INDEX IF NOT EXISTS ix_moderation_target_date ON forum_moderation_logs(target_type, target_id, created_at)');
    }

    private function upMysql(PDO $pdo): void
    {
        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `forums` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(160) NOT NULL,
                `slug` VARCHAR(180) NOT NULL,
                `description` TEXT NULL,
                `type` VARCHAR(24) NOT NULL,
                `status` VARCHAR(24) NOT NULL DEFAULT 'active',
                `position` INT NOT NULL DEFAULT 0,
                `created_by` INT UNSIGNED NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `forums_slug_unique` (`slug`),
                KEY `ix_forums_status_position` (`status`, `position`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `topics` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `forum_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `first_post_id` INT UNSIGNED NULL,
                `title` VARCHAR(240) NOT NULL,
                `slug` VARCHAR(260) NOT NULL,
                `status` VARCHAR(24) NOT NULL DEFAULT 'published',
                `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
                `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
                `last_post_at` DATETIME NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `topics_forum_slug_unique` (`forum_id`, `slug`),
                KEY `ix_topics_forum_status_created` (`forum_id`, `status`, `created_at`, `id`),
                KEY `ix_topics_forum_pin_activity` (`forum_id`, `is_pinned`, `last_post_at`),
                CONSTRAINT `topics_forum_id_fk` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `posts` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `topic_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `content` TEXT NOT NULL,
                `status` VARCHAR(24) NOT NULL DEFAULT 'published',
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `edited_at` DATETIME NULL,
                PRIMARY KEY (`id`),
                KEY `ix_posts_topic_status_created` (`topic_id`, `status`, `created_at`, `id`),
                CONSTRAINT `posts_topic_id_fk` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `faq_submissions` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `title` VARCHAR(240) NOT NULL,
                `description` TEXT NOT NULL,
                `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
                `reviewed_by` INT UNSIGNED NULL,
                `reviewed_at` DATETIME NULL,
                `review_note` TEXT NULL,
                `published_topic_id` INT UNSIGNED NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `faq_published_topic_unique` (`published_topic_id`),
                KEY `ix_faq_submissions_status_created` (`status`, `created_at`),
                CONSTRAINT `faq_published_topic_fk` FOREIGN KEY (`published_topic_id`) REFERENCES `topics` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $pdo->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `forum_moderation_logs` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `actor_user_id` INT UNSIGNED NOT NULL,
                `action` VARCHAR(64) NOT NULL,
                `target_type` VARCHAR(32) NOT NULL,
                `target_id` INT UNSIGNED NOT NULL,
                `reason` TEXT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `ix_moderation_target_date` (`target_type`, `target_id`, `created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    private function seedGeneralForum(PDO $pdo): void
    {
        $stmt = $pdo->prepare("SELECT id FROM forums WHERE type = 'general' LIMIT 1");
        $stmt->execute();
        if ($stmt->fetchColumn() !== false) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $insert = $pdo->prepare(
            'INSERT INTO forums (name, slug, description, type, status, position, created_at, updated_at)
             VALUES (:name, :slug, :description, :type, :status, :position, :created_at, :updated_at)'
        );
        $insert->execute([
            ':name'        => 'Forum général',
            ':slug'        => 'general',
            ':description' => 'Discussions générales de la communauté.',
            ':type'        => 'general',
            ':status'      => 'active',
            ':position'    => 0,
            ':created_at'  => $now,
            ':updated_at'  => $now,
        ]);
    }
}

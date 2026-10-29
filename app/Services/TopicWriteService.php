<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\ForumDao;
use App\Dao\PostDao;
use App\Dao\TopicDao;
use App\Models\Forum;
use App\Models\Topic;
use App\Support\Slug;
use Core\Auth\Auth;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;
use PDO;
use RuntimeException;

/**
 * Création atomique sujet + premier message, et réponses.
 */
final class TopicWriteService
{
    public function __construct(
        private PDO $pdo,
        private ForumDao $forumDao,
        private TopicDao $topicDao,
        private PostDao $postDao,
        private ForumPermissionService $permissions,
    ) {
    }

    /**
     * @return array{topic: Topic, forum: Forum}
     */
    public function createTopic(string $forumSlug, string $title, string $content, Auth $auth): array
    {
        $forum = $this->forumDao->findBySlug($forumSlug);
        if ($forum === null || !$forum->isActive()) {
            throw new NotFoundException('Forum introuvable.');
        }

        if (!$this->permissions->canCreateTopic($forum, $auth)) {
            throw new AuthorizationException('Vous ne pouvez pas créer de sujet dans ce forum.');
        }

        $now = date('Y-m-d H:i:s');
        $slug = Slug::unique($title, fn (string $s): bool => $this->topicDao->slugExistsInForum($forum->id, $s));

        $this->pdo->beginTransaction();
        try {
            $topicId = $this->topicDao->insert([
                'forum_id'     => $forum->id,
                'user_id'      => (int) $auth->id(),
                'first_post_id'=> null,
                'title'        => $title,
                'slug'         => $slug,
                'status'       => 'published',
                'is_pinned'    => 0,
                'is_locked'    => 0,
                'last_post_at' => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $postId = $this->postDao->insert([
                'topic_id'   => $topicId,
                'user_id'    => (int) $auth->id(),
                'content'    => $content,
                'status'     => 'published',
                'created_at' => $now,
                'updated_at' => $now,
                'edited_at'  => null,
            ]);

            $this->topicDao->update($topicId, [
                'first_post_id' => $postId,
                'updated_at'    => $now,
            ]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        /** @var Topic $topic */
        $topic = $this->topicDao->findById($topicId);

        return ['topic' => $topic, 'forum' => $forum];
    }

    public function reply(int $topicId, string $content, Auth $auth): Topic
    {
        /** @var Topic|null $topic */
        $topic = $this->topicDao->findById($topicId);
        if ($topic === null || !$topic->isPublished()) {
            throw new NotFoundException('Sujet introuvable.');
        }

        /** @var Forum|null $forum */
        $forum = $this->forumDao->findById($topic->forum_id);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        if (!$this->permissions->canReply($forum, $topic, $auth)) {
            throw new AuthorizationException('Vous ne pouvez pas répondre à ce sujet.');
        }

        $now = date('Y-m-d H:i:s');
        $this->pdo->beginTransaction();
        try {
            $this->postDao->insert([
                'topic_id'   => $topic->id,
                'user_id'    => (int) $auth->id(),
                'content'    => $content,
                'status'     => 'published',
                'created_at' => $now,
                'updated_at' => $now,
                'edited_at'  => null,
            ]);

            $this->topicDao->update($topic->id, [
                'last_post_at' => $now,
                'updated_at'   => $now,
            ]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        /** @var Topic */
        return $this->topicDao->findById($topic->id);
    }

    /**
     * Création interne (FAQ publish) — admin déjà contrôlé par l'appelant.
     */
    public function createOfficialTopic(Forum $forum, int $userId, string $title, string $content): Topic
    {
        if (!$forum->isActive()) {
            throw new RuntimeException('Le forum FAQ doit être actif.');
        }

        $now = date('Y-m-d H:i:s');
        $slug = Slug::unique($title, fn (string $s): bool => $this->topicDao->slugExistsInForum($forum->id, $s));

        $topicId = $this->topicDao->insert([
            'forum_id'      => $forum->id,
            'user_id'       => $userId,
            'first_post_id' => null,
            'title'         => $title,
            'slug'          => $slug,
            'status'        => 'published',
            'is_pinned'     => 0,
            'is_locked'     => 1,
            'last_post_at'  => $now,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $postId = $this->postDao->insert([
            'topic_id'   => $topicId,
            'user_id'    => $userId,
            'content'    => $content,
            'status'     => 'published',
            'created_at' => $now,
            'updated_at' => $now,
            'edited_at'  => null,
        ]);

        $this->topicDao->update($topicId, [
            'first_post_id' => $postId,
            'updated_at'    => $now,
        ]);

        /** @var Topic */
        return $this->topicDao->findById($topicId);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\ForumModerationLogDao;
use App\Dao\PostDao;
use App\Dao\TopicDao;
use App\Models\Post;
use App\Models\Topic;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;
use Core\Auth\Auth;

final class ModerationService
{
    public function __construct(
        private TopicDao $topicDao,
        private PostDao $postDao,
        private ForumModerationLogDao $logDao,
        private ForumPermissionService $permissions,
    ) {
    }

    public function lockTopic(int $topicId, Auth $auth, ?string $reason = null): Topic
    {
        $this->assertModerator($auth);
        $topic = $this->requireTopic($topicId);
        $this->topicDao->update($topic->id, [
            'is_locked'  => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logDao->log((int) $auth->id(), 'topic.lock', 'topic', $topic->id, $reason);

        /** @var Topic */
        return $this->topicDao->findById($topic->id);
    }

    public function unlockTopic(int $topicId, Auth $auth, ?string $reason = null): Topic
    {
        $this->assertModerator($auth);
        $topic = $this->requireTopic($topicId);
        $this->topicDao->update($topic->id, [
            'is_locked'  => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logDao->log((int) $auth->id(), 'topic.unlock', 'topic', $topic->id, $reason);

        /** @var Topic */
        return $this->topicDao->findById($topic->id);
    }

    public function pinTopic(int $topicId, Auth $auth, ?string $reason = null): Topic
    {
        $this->assertModerator($auth);
        $topic = $this->requireTopic($topicId);
        $this->topicDao->update($topic->id, [
            'is_pinned'  => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logDao->log((int) $auth->id(), 'topic.pin', 'topic', $topic->id, $reason);

        /** @var Topic */
        return $this->topicDao->findById($topic->id);
    }

    public function unpinTopic(int $topicId, Auth $auth, ?string $reason = null): Topic
    {
        $this->assertModerator($auth);
        $topic = $this->requireTopic($topicId);
        $this->topicDao->update($topic->id, [
            'is_pinned'  => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logDao->log((int) $auth->id(), 'topic.unpin', 'topic', $topic->id, $reason);

        /** @var Topic */
        return $this->topicDao->findById($topic->id);
    }

    public function hidePost(int $postId, Auth $auth, ?string $reason = null): Post
    {
        $this->assertModerator($auth);
        /** @var Post|null $post */
        $post = $this->postDao->findById($postId);
        if ($post === null) {
            throw new NotFoundException("Message #{$postId} introuvable.");
        }

        $this->postDao->update($post->id, [
            'status'     => 'hidden',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logDao->log((int) $auth->id(), 'post.hide', 'post', $post->id, $reason);

        /** @var Post */
        return $this->postDao->findById($post->id);
    }

    private function assertModerator(Auth $auth): void
    {
        if (!$this->permissions->canModerate($auth)) {
            throw new AuthorizationException('Action de modération réservée aux administrateurs.');
        }
    }

    private function requireTopic(int $topicId): Topic
    {
        /** @var Topic|null $topic */
        $topic = $this->topicDao->findById($topicId);
        if ($topic === null) {
            throw new NotFoundException("Sujet #{$topicId} introuvable.");
        }

        return $topic;
    }
}

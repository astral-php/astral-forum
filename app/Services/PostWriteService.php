<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\ForumDao;
use App\Dao\PostDao;
use App\Dao\TopicDao;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Core\Auth\Auth;
use Core\Exception\AuthorizationException;
use Core\Exception\NotFoundException;

/**
 * Édition et soft-delete des messages (auteur + délai ; admin illimité).
 */
final class PostWriteService
{
    public function __construct(
        private PostDao $postDao,
        private TopicDao $topicDao,
        private ForumDao $forumDao,
        private ForumPermissionService $permissions,
    ) {
    }

    /**
     * @return array{post: Post, topic: Topic, forum: Forum}
     */
    public function requireContext(int $postId): array
    {
        /** @var Post|null $post */
        $post = $this->postDao->findById($postId);
        if ($post === null) {
            throw new NotFoundException('Message introuvable.');
        }

        /** @var Topic|null $topic */
        $topic = $this->topicDao->findById($post->topic_id);
        if ($topic === null) {
            throw new NotFoundException('Sujet introuvable.');
        }

        /** @var Forum|null $forum */
        $forum = $this->forumDao->findById($topic->forum_id);
        if ($forum === null) {
            throw new NotFoundException('Forum introuvable.');
        }

        return ['post' => $post, 'topic' => $topic, 'forum' => $forum];
    }

    public function update(int $postId, string $content, Auth $auth): Post
    {
        $ctx = $this->requireContext($postId);
        $post = $ctx['post'];

        if (!$post->isPublished()) {
            throw new AuthorizationException('Ce message ne peut plus être modifié.');
        }

        if (!$this->permissions->canEditPost($post, $auth)) {
            throw new AuthorizationException('Vous ne pouvez pas modifier ce message.');
        }

        if ($ctx['topic']->isLocked() && !$this->permissions->canModerate($auth)) {
            throw new AuthorizationException('Sujet verrouillé.');
        }

        $now = date('Y-m-d H:i:s');
        $this->postDao->update($post->id, [
            'content' => $content,
            'edited_at' => $now,
            'updated_at' => $now,
        ]);

        /** @var Post */
        return $this->postDao->findById($post->id);
    }

    public function softDelete(int $postId, Auth $auth): Post
    {
        $ctx = $this->requireContext($postId);
        $post = $ctx['post'];

        if (!$post->isPublished()) {
            throw new AuthorizationException('Ce message est déjà retiré.');
        }

        if (!$this->permissions->canSoftDeletePost($post, $auth)) {
            throw new AuthorizationException('Vous ne pouvez pas supprimer ce message.');
        }

        if ($ctx['topic']->isLocked() && !$this->permissions->canModerate($auth)) {
            throw new AuthorizationException('Sujet verrouillé.');
        }

        // Premier message : soft-delete interdit (même modo) — admin utilise hide.
        if ($ctx['topic']->first_post_id === $post->id) {
            throw new AuthorizationException(
                'Le premier message du sujet ne peut pas être soft-supprimé. Utilisez le masquage admin.'
            );
        }

        $now = date('Y-m-d H:i:s');
        $this->postDao->update($post->id, [
            'status' => 'deleted',
            'updated_at' => $now,
        ]);

        /** @var Post */
        return $this->postDao->findById($post->id);
    }
}

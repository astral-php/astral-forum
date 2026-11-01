<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use App\Services\ForumPermissionService;
use Core\Auth\Auth;
use Core\Auth\Role;
use Core\Session;
use PHPUnit\Framework\TestCase;

final class ForumPermissionServiceTest extends TestCase
{
    private ForumPermissionService $permissions;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->permissions = new ForumPermissionService(30);
    }

    public function test_guest_cannot_create_topic(): void
    {
        $forum = new Forum();
        $forum->status = 'active';
        $forum->type = 'general';

        $auth = new Auth(new Session());
        $this->assertFalse($this->permissions->canCreateTopic($forum, $auth));
    }

    public function test_member_cannot_reply_on_faq(): void
    {
        $forum = new Forum();
        $forum->status = 'active';
        $forum->type = 'faq';

        $topic = new Topic();
        $topic->status = 'published';
        $topic->is_locked = 0;

        $user = new User();
        $user->id = 1;
        $user->name = 'Member';
        $user->email = 'm@example.com';
        $user->role = Role::User->value;

        $auth = new Auth(new Session());
        $auth->login($user);

        $this->assertFalse($this->permissions->canReply($forum, $topic, $auth));
    }

    public function test_member_can_create_in_general(): void
    {
        $forum = new Forum();
        $forum->status = 'active';
        $forum->type = 'general';

        $user = new User();
        $user->id = 2;
        $user->name = 'Member';
        $user->email = 'm2@example.com';
        $user->role = Role::User->value;

        $auth = new Auth(new Session());
        $auth->login($user);

        $this->assertTrue($this->permissions->canCreateTopic($forum, $auth));
    }

    public function test_author_can_edit_within_window(): void
    {
        $post = new Post();
        $post->user_id = 2;
        $post->status = 'published';
        $post->created_at = date('Y-m-d H:i:s');

        $user = new User();
        $user->id = 2;
        $user->name = 'Author';
        $user->email = 'a@example.com';
        $user->role = Role::User->value;

        $auth = new Auth(new Session());
        $auth->login($user);

        $this->assertTrue($this->permissions->canEditPost($post, $auth));
        $this->assertTrue($this->permissions->canSoftDeletePost($post, $auth));
    }

    public function test_author_cannot_edit_outside_window(): void
    {
        $post = new Post();
        $post->user_id = 2;
        $post->status = 'published';
        $post->created_at = date('Y-m-d H:i:s', time() - 3600);

        $user = new User();
        $user->id = 2;
        $user->name = 'Author';
        $user->email = 'a@example.com';
        $user->role = Role::User->value;

        $auth = new Auth(new Session());
        $auth->login($user);

        $this->assertFalse($this->permissions->canEditPost($post, $auth));
    }

    public function test_admin_can_edit_outside_window(): void
    {
        $post = new Post();
        $post->user_id = 9;
        $post->status = 'published';
        $post->created_at = date('Y-m-d H:i:s', time() - 86400);

        $user = new User();
        $user->id = 1;
        $user->name = 'Admin';
        $user->email = 'admin@example.com';
        $user->role = Role::Admin->value;

        $auth = new Auth(new Session());
        $auth->login($user);

        $this->assertTrue($this->permissions->canEditPost($post, $auth));
    }
}

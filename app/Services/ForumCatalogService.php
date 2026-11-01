<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\ForumDao;
use App\Models\Forum;
use Astral\Cache\CacheRepository;

/**
 * Liste publique des forums via cache d’IDs (pas d’objets en cache fichier).
 */
final class ForumCatalogService
{
    private const CACHE_KEY = 'forums.public_active_ids';
    private const TTL = 300;

    public function __construct(
        private ForumDao $forumDao,
        private CacheRepository $cache,
    ) {
    }

    /** @return list<Forum> */
    public function publicActive(): array
    {
        /** @var list<int|string> $ids */
        $ids = $this->cache->remember(self::CACHE_KEY, self::TTL, function (): array {
            return array_map(
                static fn (Forum $f): int => $f->id,
                $this->forumDao->findPublicActive(),
            );
        });

        if ($ids === []) {
            return [];
        }

        $byId = [];
        foreach ($this->forumDao->findByIds(array_map('intval', $ids)) as $forum) {
            $byId[$forum->id] = $forum;
        }

        $ordered = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if (isset($byId[$id]) && $byId[$id]->isActive()) {
                $ordered[] = $byId[$id];
            }
        }

        return $ordered;
    }

    public function forgetPublicList(): void
    {
        $this->cache->forget(self::CACHE_KEY);
        // Compat clés antérieures
        $this->cache->forget('forums.public_active');
    }
}

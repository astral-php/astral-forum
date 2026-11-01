<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\ForumDao;
use App\Dao\TopicDao;
use App\Models\Forum;
use App\Models\Topic;

/**
 * Recherche paginée sujets (titre) + messages publiés.
 */
final class SearchService
{
    public function __construct(
        private TopicDao $topicDao,
        private ForumDao $forumDao,
    ) {
    }

    /**
     * @return array{
     *   query: string,
     *   data: list<array{topic: Topic, forum: Forum, match: string}>,
     *   total: int,
     *   pages: int,
     *   current: int,
     *   per_page: int
     * }
     */
    public function search(string $query, int $page = 1, int $perPage = 15): array
    {
        $query = trim($query);
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));

        if ($query === '' || mb_strlen($query) < 2) {
            return [
                'query' => $query,
                'data' => [],
                'total' => 0,
                'pages' => 0,
                'current' => $page,
                'per_page' => $perPage,
            ];
        }

        $result = $this->topicDao->searchPublished($query, $page, $perPage);
        $items = [];

        foreach ($result['data'] as $row) {
            /** @var Topic $topic */
            $topic = $row['topic'];
            /** @var Forum|null $forum */
            $forum = $this->forumDao->findById($topic->forum_id);
            if ($forum === null || !$forum->isActive()) {
                continue;
            }

            $items[] = [
                'topic' => $topic,
                'forum' => $forum,
                'match' => (string) $row['match'],
            ];
        }

        return [
            'query' => $query,
            'data' => $items,
            'total' => $result['total'],
            'pages' => $result['pages'],
            'current' => $result['current'],
            'per_page' => $result['per_page'],
        ];
    }
}

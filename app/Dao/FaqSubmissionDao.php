<?php

declare(strict_types=1);

namespace App\Dao;

use App\Models\FaqSubmission;
use Database\AbstractDao;

/**
 * @extends AbstractDao<FaqSubmission>
 */
final class FaqSubmissionDao extends AbstractDao
{
    protected function getTable(): string
    {
        return 'faq_submissions';
    }

    protected function getModelClass(): string
    {
        return FaqSubmission::class;
    }

    /** @return list<FaqSubmission> */
    public function findByUser(int $userId): array
    {
        /** @var list<FaqSubmission> */
        return $this->query(
            "SELECT * FROM {$this->getTable()} WHERE user_id = :user_id ORDER BY created_at DESC, id DESC",
            [':user_id' => $userId],
        );
    }

    /**
     * @return array{data: list<FaqSubmission>, total: int, pages: int, current: int, per_page: int}
     */
    public function paginateForAdmin(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '1=1';

        if ($status !== null && $status !== '') {
            $where = 'status = :status';
            $params[':status'] = $status;
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->getTable()} WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        /** @var list<FaqSubmission> $data */
        $data = $this->query(
            "SELECT * FROM {$this->getTable()} WHERE {$where}
             ORDER BY created_at DESC, id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params,
        );

        return [
            'data'     => $data,
            'total'    => $total,
            'pages'    => $total > 0 ? (int) ceil($total / $perPage) : 0,
            'current'  => $page,
            'per_page' => $perPage,
        ];
    }
}

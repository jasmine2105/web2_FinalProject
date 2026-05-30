<?php

declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

class Task extends Model
{
    protected string $table = 'tasks';

    public function findByProject(int $projectId): array
    {
        return $this->queryBuilder->selectWhere($this->table, 'project_id', $projectId);
    }

    public function deleteByProject(int $projectId): bool
    {
        return $this->queryBuilder->deleteWhere($this->table, 'project_id', $projectId);
    }

    public function titleExistsInProgress(string $title): bool
    {
        $rows = $this->queryBuilder->selectWhere($this->table, 'title', $title);

        foreach ($rows as $row) {
            if ($row['status'] === 'in_progress') {
                return true;
            }
        }

        return false;
    }
}

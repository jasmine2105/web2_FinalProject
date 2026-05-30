<?php

declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

class Project extends Model
{
    protected string $table = 'projects';

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $rows = $this->queryBuilder->selectWhere($this->table, 'name', $name);

        foreach ($rows as $row) {
            if ($excludeId === null || (int) $row['id'] !== $excludeId) {
                return true;
            }
        }

        return false;
    }
}

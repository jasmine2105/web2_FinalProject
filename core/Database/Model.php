<?php

declare(strict_types=1);

namespace Core\Database;

abstract class Model
{
    protected string $table;

    public function __construct(protected readonly QueryBuilder $queryBuilder) {}

    public function find(int $id): ?array
    {
        return $this->queryBuilder->selectById($this->table, $id);
    }

    public function all(): array
    {
        return $this->queryBuilder->selectAll($this->table);
    }

    public function save(array $data): bool
    {
        return $this->queryBuilder->insert($this->table, $data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->queryBuilder->update($this->table, $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->queryBuilder->delete($this->table, $id);
    }
}

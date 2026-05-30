<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

class QueryBuilder
{
    public function __construct(private readonly PDO $pdo) {}

    public function selectAll(string $table): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function selectById(string $table, int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function insert(string $table, array $data): bool
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql          = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt         = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update(string $table, int $id, array $data): bool
    {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $setParts);
        $sql       = "UPDATE {$table} SET {$setString} WHERE id = :id";
        $data['id'] = $id;
        $stmt       = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(string $table, int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function selectWhere(string $table, string $column, mixed $value): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }

    public function deleteWhere(string $table, string $column, mixed $value): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE {$column} = :value");
        return $stmt->execute(['value' => $value]);
    }

    public function updateWhere(string $table, string $whereColumn, mixed $whereValue, array $data): bool
    {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $sql                 = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$whereColumn} = :__where_val";
        $data['__where_val'] = $whereValue;
        $stmt                = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}

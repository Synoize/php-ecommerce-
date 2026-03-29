<?php

declare(strict_types=1);

class BoxOptionModel extends BaseModel
{
    public function all(): array
    {
        return $this->pdo->query(
            'SELECT * FROM box_options ORDER BY name ASC'
        )->fetchAll();
    }

    public function activeAll(): array
    {
        return $this->pdo->query(
            'SELECT * FROM box_options WHERE is_active = 1 ORDER BY name ASC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM box_options WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): int
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO box_options (name, image, price, is_active)
                 VALUES (:name, :image, :price, :is_active)'
            );
            $stmt->execute([
                'name' => $data['name'],
                'image' => $data['image'] ?: null,
                'price' => $data['price'],
                'is_active' => $data['is_active'],
            ]);
            return (int) $this->pdo->lastInsertId();
        }

        $stmt = $this->pdo->prepare(
            'UPDATE box_options
             SET name = :name, image = :image, price = :price, is_active = :is_active
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'image' => $data['image'] ?: null,
            'price' => $data['price'],
            'is_active' => $data['is_active'],
        ]);

        return $id;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM box_options WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

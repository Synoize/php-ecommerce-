<?php

declare(strict_types=1);

class CategoryModel extends BaseModel
{
    public function all(): array
    {
        return $this->pdo->query(
            'SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id
             ORDER BY c.name ASC'
        )->fetchAll();
    }

    public function featured(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare('INSERT INTO categories (name, image) VALUES (:name, :image)');
            $stmt->execute(['name' => $data['name'], 'image' => $data['image'] ?: null]);
            return;
        }

        $stmt = $this->pdo->prepare('UPDATE categories SET name = :name, image = :image WHERE id = :id');
        $stmt->execute(['id' => $id, 'name' => $data['name'], 'image' => $data['image'] ?: null]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

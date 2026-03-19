<?php

declare(strict_types=1);

class ProductModel extends BaseModel
{
    public function featured(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.name AS category_name,
                    COALESCE(AVG(r.rating), 0) AS avg_rating,
                    COUNT(r.id) AS review_count
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.is_active = 1
             GROUP BY p.id
             ORDER BY p.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function priceRange(): array
    {
        $row = $this->pdo->query(
            'SELECT COALESCE(MIN(price), 0) AS min_price, COALESCE(MAX(price), 0) AS max_price
             FROM products
             WHERE is_active = 1'
        )->fetch();

        return [
            'min' => (float) ($row['min_price'] ?? 0),
            'max' => (float) ($row['max_price'] ?? 0),
        ];
    }

    public function search(array $filters): array
    {
        $where = ['p.is_active = 1'];
        $params = [];

        $categoryIds = array_values(array_filter(
            array_map('intval', (array) ($filters['category_ids'] ?? [])),
            static fn(int $id): bool => $id > 0
        ));

        if ($categoryIds === [] && !empty($filters['category_id'])) {
            $categoryIds[] = (int) $filters['category_id'];
        }

        if ($categoryIds !== []) {
            $placeholders = [];
            foreach ($categoryIds as $index => $categoryId) {
                $key = 'category_' . $index;
                $placeholders[] = ':' . $key;
                $params[$key] = $categoryId;
            }
            $where[] = 'p.category_id IN (' . implode(', ', $placeholders) . ')';
        }

        if (!empty($filters['query'])) {
            $where[] = '(p.name LIKE :query OR p.description LIKE :query)';
            $params['query'] = '%' . $filters['query'] . '%';
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'p.price >= :min_price';
            $params['min_price'] = (float) $filters['min_price'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'p.price <= :max_price';
            $params['max_price'] = (float) $filters['max_price'];
        }

        $sortSql = match ($filters['sort'] ?? '') {
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'rating' => 'avg_rating DESC',
            default => 'p.created_at DESC',
        };

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, min(24, (int) ($filters['per_page'] ?? 8)));
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->pdo->prepare('SELECT COUNT(*) FROM products p WHERE ' . implode(' AND ', $where));
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $sql = 'SELECT p.*, c.name AS category_name,
                       COALESCE(AVG(r.rating), 0) AS avg_rating,
                       COUNT(r.id) AS review_count
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN reviews r ON r.product_id = p.id
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY p.id
                ORDER BY ' . $sortSql . '
                LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.name AS category_name,
                    COALESCE(AVG(r.rating), 0) AS avg_rating,
                    COUNT(r.id) AS review_count
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.id = :id
             GROUP BY p.id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        if (!$product) {
            return null;
        }

        $product['images'] = $this->images($id);
        if ($product['images'] === []) {
            $product['images'][] = ['image_url' => $product['image']];
        }
        $product['box_options'] = $this->boxOptions($id);
        $product['box_options_admin'] = $this->boxOptionsAdmin($id);

        return $product;
    }

    public function images(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM product_images WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function boxOptions(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM product_box_options WHERE product_id = :product_id AND is_active = 1 ORDER BY id ASC'
        );
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function boxOptionsAdmin(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM product_box_options WHERE product_id = :product_id ORDER BY id ASC'
        );
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function related(int $categoryId, int $excludeId, int $limit = 4): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM products
             WHERE is_active = 1 AND category_id = :category_id AND id != :exclude_id
             ORDER BY created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('exclude_id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function adminAll(): array
    {
        return $this->pdo->query(
            'SELECT p.*, c.name AS category_name,
                    (SELECT COUNT(*) FROM product_images pi WHERE pi.product_id = p.id) AS image_count,
                    (SELECT COUNT(*) FROM product_box_options pbo WHERE pbo.product_id = p.id) AS box_count
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    public function save(array $data, ?int $id = null): int
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO products (name, description, category_id, price, stock, image, is_active)
                 VALUES (:name, :description, :category_id, :price, :stock, :image, :is_active)'
            );
            $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'] ?: null,
                'category_id' => $data['category_id'] ?: null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image' => $data['image'] ?: null,
                'is_active' => $data['is_active'],
            ]);
            $id = (int) $this->pdo->lastInsertId();
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE products
                 SET name = :name, description = :description, category_id = :category_id,
                     price = :price, stock = :stock, image = :image, is_active = :is_active
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'] ?: null,
                'category_id' => $data['category_id'] ?: null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image' => $data['image'] ?: null,
                'is_active' => $data['is_active'],
            ]);
        }

        $this->syncImages($id, $data['gallery'] ?? []);
        $this->syncBoxOptions($id, $data['box_options'] ?? []);
        return $id;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function syncImages(int $productId, array $gallery): void
    {
        $this->pdo->prepare('DELETE FROM product_images WHERE product_id = :product_id')
            ->execute(['product_id' => $productId]);

        $stmt = $this->pdo->prepare(
            'INSERT INTO product_images (product_id, image_url, sort_order) VALUES (:product_id, :image_url, :sort_order)'
        );

        foreach ($gallery as $sort => $imageUrl) {
            $imageUrl = trim((string) $imageUrl);
            if ($imageUrl === '') {
                continue;
            }

            $stmt->execute([
                'product_id' => $productId,
                'image_url' => $imageUrl,
                'sort_order' => $sort,
            ]);
        }
    }

    private function syncBoxOptions(int $productId, array $boxOptions): void
    {
        $this->pdo->prepare('DELETE FROM product_box_options WHERE product_id = :product_id')
            ->execute(['product_id' => $productId]);

        $stmt = $this->pdo->prepare(
            'INSERT INTO product_box_options (product_id, name, image, price, is_active)
             VALUES (:product_id, :name, :image, :price, :is_active)'
        );

        foreach ($boxOptions as $option) {
            $name = trim((string) ($option['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $stmt->execute([
                'product_id' => $productId,
                'name' => $name,
                'image' => trim((string) ($option['image'] ?? '')) ?: null,
                'price' => (float) ($option['price'] ?? 0),
                'is_active' => !empty($option['is_active']) ? 1 : 0,
            ]);
        }
    }
}

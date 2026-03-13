<?php

declare(strict_types=1);

class CouponModel extends BaseModel
{
    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM coupons ORDER BY created_at DESC')->fetchAll();
    }

    public function validateCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM coupons
             WHERE code = :code AND is_active = 1
               AND (valid_from IS NULL OR valid_from <= CURDATE())
               AND (valid_to IS NULL OR valid_to >= CURDATE())
             LIMIT 1'
        );
        $stmt->execute(['code' => strtoupper(trim($code))]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM coupons WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        $payload = [
            'code' => strtoupper(trim((string) $data['code'])),
            'discount_percent' => (int) $data['discount_percent'],
            'valid_from' => $data['valid_from'] ?: null,
            'valid_to' => $data['valid_to'] ?: null,
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ];

        if ($id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO coupons (code, discount_percent, valid_from, valid_to, is_active)
                 VALUES (:code, :discount_percent, :valid_from, :valid_to, :is_active)'
            );
            $stmt->execute($payload);
            return;
        }

        $payload['id'] = $id;
        $stmt = $this->pdo->prepare(
            'UPDATE coupons
             SET code = :code, discount_percent = :discount_percent, valid_from = :valid_from,
                 valid_to = :valid_to, is_active = :is_active
             WHERE id = :id'
        );
        $stmt->execute($payload);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM coupons WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

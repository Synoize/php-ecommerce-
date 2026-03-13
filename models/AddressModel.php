<?php

declare(strict_types=1);

class AddressModel extends BaseModel
{
    public function forUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findOwned(int $userId, int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM addresses WHERE id = :id AND user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function save(int $userId, array $data): int
    {
        if (!empty($data['is_default'])) {
            $this->pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :user_id')
                ->execute(['user_id' => $userId]);
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO addresses (user_id, full_name, phone, address_line, city, state, pincode, country, is_default)
             VALUES (:user_id, :full_name, :phone, :address_line, :city, :state, :pincode, :country, :is_default)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'address_line' => $data['address_line'],
            'city' => $data['city'],
            'state' => $data['state'],
            'pincode' => $data['pincode'],
            'country' => $data['country'] ?: 'India',
            'is_default' => !empty($data['is_default']) ? 1 : 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}

<?php

declare(strict_types=1);

class UserModel extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, phone, role) VALUES (:name, :email, :password, :phone, :role)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => $data['phone'] ?: null,
            'role' => $data['role'] ?? 'user',
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT u.id, u.name, u.email, u.phone, u.role, u.created_at,
                    COUNT(DISTINCT o.id) AS orders_count,
                    COUNT(DISTINCT a.id) AS address_count
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id
             LEFT JOIN addresses a ON a.user_id = u.id
             GROUP BY u.id
             ORDER BY u.created_at DESC'
        )->fetchAll();
    }

    public function updateRole(int $id, string $role): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'role' => $role === 'admin' ? 'admin' : 'user',
        ]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}

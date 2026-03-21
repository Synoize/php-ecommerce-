<?php

declare(strict_types=1);

class PasswordResetModel extends BaseModel
{
    public function create(int $userId): array
    {
        $this->pdo->prepare('UPDATE password_resets SET used_at = CURRENT_TIMESTAMP WHERE user_id = :user_id AND used_at IS NULL')
            ->execute(['user_id' => $userId]);

        $selector = bin2hex(random_bytes(8));
        $token = bin2hex(random_bytes(32));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO password_resets (user_id, selector, token_hash, expires_at)
             VALUES (:user_id, :selector, :token_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
        );
        $stmt->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'token_hash' => $tokenHash,
        ]);

        return [
            'selector' => $selector,
            'token' => $token,
        ];
    }

    public function findValid(string $selector, string $token): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT pr.*, u.email
             FROM password_resets pr
             INNER JOIN users u ON u.id = pr.user_id
             WHERE pr.selector = :selector AND pr.used_at IS NULL AND pr.expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute(['selector' => $selector]);
        $reset = $stmt->fetch();

        if (!$reset || !password_verify($token, (string) $reset['token_hash'])) {
            return null;
        }

        return $reset;
    }

    public function consume(string $selector, string $token, string $newPassword): bool
    {
        $reset = $this->findValid($selector, $token);
        if (!$reset) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $userStmt = $this->pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
            $userStmt->execute([
                'id' => $reset['user_id'],
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);

            $resetStmt = $this->pdo->prepare('UPDATE password_resets SET used_at = CURRENT_TIMESTAMP WHERE id = :id');
            $resetStmt->execute(['id' => $reset['id']]);

            $cleanupStmt = $this->pdo->prepare('UPDATE password_resets SET used_at = CURRENT_TIMESTAMP WHERE user_id = :user_id AND id != :id AND used_at IS NULL');
            $cleanupStmt->execute([
                'user_id' => $reset['user_id'],
                'id' => $reset['id'],
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

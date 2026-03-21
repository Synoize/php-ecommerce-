<?php

declare(strict_types=1);

class SlideModel extends BaseModel
{
    public function all(): array
    {
        return $this->pdo->query(
            'SELECT id, type, file_path, title, description, button_name, button_link, created_at
             FROM slides
             ORDER BY id DESC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM slides WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        $payload = [
            'type' => in_array(($data['type'] ?? 'image'), ['image', 'video'], true) ? $data['type'] : 'image',
            'file_path' => trim((string) ($data['file_path'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')) ?: null,
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'button_name' => trim((string) ($data['button_name'] ?? '')) ?: null,
            'button_link' => trim((string) ($data['button_link'] ?? '')) ?: null,
        ];

        if ($id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO slides (type, file_path, title, description, button_name, button_link)
                 VALUES (:type, :file_path, :title, :description, :button_name, :button_link)'
            );
            $stmt->execute($payload);
            return;
        }

        $payload['id'] = $id;
        $stmt = $this->pdo->prepare(
            'UPDATE slides
             SET type = :type, file_path = :file_path, title = :title, description = :description,
                 button_name = :button_name, button_link = :button_link
             WHERE id = :id'
        );
        $stmt->execute($payload);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM slides WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

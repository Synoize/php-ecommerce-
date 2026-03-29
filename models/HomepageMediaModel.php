<?php

declare(strict_types=1);

class HomepageMediaModel extends BaseModel
{
    public function featuredProductVideos(): array
    {
        return $this->filePaths('featuredProductsVideo');
    }

    public function userReviews(): array
    {
        return $this->filePaths('userReview');
    }

    public function allFeaturedProductVideos(): array
    {
        return $this->rows('featuredProductsVideo');
    }

    public function allUserReviews(): array
    {
        return $this->rows('userReview');
    }

    public function addFeaturedProductVideo(string $filePath): void
    {
        $this->insert('featuredProductsVideo', $filePath);
    }

    public function addUserReview(string $filePath): void
    {
        $this->insert('userReview', $filePath);
    }

    public function deleteFeaturedProductVideo(int $id): void
    {
        $this->delete('featuredProductsVideo', $id);
    }

    public function deleteUserReview(int $id): void
    {
        $this->delete('userReview', $id);
    }

    private function rows(string $table): array
    {
        $this->assertTable($table);

        $statement = $this->pdo->query(
            "SELECT id, file_path, created_at
             FROM {$table}
             WHERE file_path <> ''
             ORDER BY id DESC"
        );

        return $statement->fetchAll();
    }

    private function filePaths(string $table): array
    {
        return array_map(
            static fn(array $row): string => (string) $row['file_path'],
            $this->rows($table)
        );
    }

    private function insert(string $table, string $filePath): void
    {
        $this->assertTable($table);

        $filePath = trim($filePath);
        if ($filePath === '') {
            throw new InvalidArgumentException('File path is required.');
        }

        $statement = $this->pdo->prepare("INSERT INTO {$table} (file_path) VALUES (:file_path)");
        $statement->execute(['file_path' => $filePath]);
    }

    private function delete(string $table, int $id): void
    {
        $this->assertTable($table);

        $statement = $this->pdo->prepare("DELETE FROM {$table} WHERE id = :id");
        $statement->execute(['id' => $id]);
    }

    private function assertTable(string $table): void
    {
        $allowedTables = ['featuredProductsVideo', 'userReview'];
        if (!in_array($table, $allowedTables, true)) {
            throw new InvalidArgumentException('Unsupported homepage media table.');
        }
    }
}

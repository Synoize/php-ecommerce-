<?php

declare(strict_types=1);

class SlideModel extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, type, file_path, title, description, button_name, button_link
             FROM slides
             ORDER BY id DESC'
        );

        return $stmt->fetchAll();
    }
}


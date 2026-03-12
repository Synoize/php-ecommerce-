<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isPost(): bool
{
    return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function uploadUrlOrLocal(string $imageValue): string
{
    $imageValue = trim($imageValue);
    if ($imageValue === '') {
        return asset('images/uploads/');
    }
    if (preg_match('/^https?:\/\//i', $imageValue)) {
        return $imageValue;
    }
    // treat as local filename/path under uploads
    return asset('images/uploads/' . ltrim($imageValue, '/'));
}

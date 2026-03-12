<?php

declare(strict_types=1);

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('danger', 'Please login to continue.');
        redirect('/user/login.php');
    }
}

function isAdmin(): bool
{
    return isLoggedIn() && (($_SESSION['user']['role'] ?? '') === 'admin');
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('danger', 'Admin access required.');
        redirect('/admin/admin_login.php');
    }
}

<?php

declare(strict_types=1);

class AuthController
{
    private UserModel $users;
    private PasswordResetModel $passwordResets;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->passwordResets = new PasswordResetModel();
    }

    public function register(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $phone = trim((string) ($data['phone'] ?? ''));

        if ($name === '' || $email === '' || $password === '') {
            return ['ok' => false, 'message' => 'Name, email, and password are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'Enter a valid email address.'];
        }

        if (strlen($password) < 6) {
            return ['ok' => false, 'message' => 'Password must be at least 6 characters.'];
        }

        if ($this->users->findByEmail($email)) {
            return ['ok' => false, 'message' => 'An account already exists with this email.'];
        }

        $userId = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
        ]);

        $user = $this->users->find($userId);
        unset($user['password']);
        $_SESSION['user'] = $user;

        return ['ok' => true, 'message' => 'Registration completed successfully.'];
    }

    public function login(array $data): array
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, (string) $user['password'])) {
            return ['ok' => false, 'message' => 'Invalid email or password.'];
        }

        unset($user['password']);
        $_SESSION['user'] = $user;
        return ['ok' => true, 'message' => 'Signed in successfully.'];
    }

    public function requestPasswordReset(string $email): array
    {
        $email = strtolower(trim($email));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'Enter a valid email address.'];
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            return [
                'ok' => true,
                'message' => 'If the email exists, a reset link has been generated.',
            ];
        }

        $reset = $this->passwordResets->create((int) $user['id']);

        return [
            'ok' => true,
            'message' => 'Password reset link generated.',
            'reset_link' => app_url('user/reset_password.php?selector=' . $reset['selector'] . '&token=' . $reset['token']),
        ];
    }

    public function resetPassword(string $selector, string $token, string $password, string $confirmPassword): array
    {
        if ($selector === '' || $token === '') {
            return ['ok' => false, 'message' => 'Invalid password reset link.'];
        }

        if (strlen($password) < 6) {
            return ['ok' => false, 'message' => 'Password must be at least 6 characters.'];
        }

        if ($password !== $confirmPassword) {
            return ['ok' => false, 'message' => 'Passwords do not match.'];
        }

        if (!$this->passwordResets->consume($selector, $token, $password)) {
            return ['ok' => false, 'message' => 'Reset link is invalid or expired.'];
        }

        return ['ok' => true, 'message' => 'Password updated successfully. Please sign in.'];
    }

    public function logout(): void
    {
        unset($_SESSION['user'], $_SESSION['checkout_coupon'], $_SESSION['pending_order_id'], $_SESSION['pending_checkout']);
        session_regenerate_id(true);
    }
}

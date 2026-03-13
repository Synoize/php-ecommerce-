<?php
require_once __DIR__ . '/../config/bootstrap.php';
(new AuthController())->logout();
set_flash('success', 'Logged out successfully.');
redirect('');


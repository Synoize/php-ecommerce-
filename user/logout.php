<?php
require_once __DIR__ . '/../includes/config.php';
unset($_SESSION['user']);
setFlash('success', 'Logged out successfully.');
redirect('/index.php');

<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();
(new ProductModel())->delete((int) ($_GET['id'] ?? 0));
set_flash('success', 'Product deleted.');
redirect('admin/manage_products.php');


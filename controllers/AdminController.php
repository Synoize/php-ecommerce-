<?php

declare(strict_types=1);

class AdminController
{
    public function dashboard(): array
    {
        $orders = new OrderModel();
        $users = new UserModel();
        $products = new ProductModel();
        $categories = new CategoryModel();

        return [
            'orders' => $orders->dashboardStats(),
            'users' => $users->count(),
            'products' => count($products->adminAll()),
            'categories' => count($categories->all()),
        ];
    }
}

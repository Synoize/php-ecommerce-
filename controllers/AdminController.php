<?php

declare(strict_types=1);

class AdminController
{
    public function dashboard(): array
    {
        $orders = new OrderModel();
        $products = new ProductModel();

        return [
            'orders' => $orders->dashboardStats(),
            'inventory' => $products->dashboardStats(),
        ];
    }
}

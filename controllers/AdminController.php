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
        $coupons = new CouponModel();
        $reviews = new ReviewModel();
        $slides = new SlideModel();
        $payments = new PaymentModel();
        $addresses = new AddressModel();
        $carts = new CartModel();
        $wishlists = new WishlistModel();

        return [
            'orders' => $orders->dashboardStats(),
            'users' => $users->count(),
            'products' => count($products->adminAll()),
            'categories' => count($categories->all()),
            'coupons' => count($coupons->all()),
            'reviews' => count($reviews->all()),
            'slides' => count($slides->all()),
            'payments' => count($payments->all()),
            'addresses' => count($addresses->all()),
            'carts' => count($carts->adminAll()),
            'wishlists' => count($wishlists->adminAll()),
        ];
    }
}

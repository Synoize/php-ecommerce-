<?php

declare(strict_types=1);

class StoreController
{
    public ProductModel $products;
    public CategoryModel $categories;
    public CartModel $cart;
    public WishlistModel $wishlist;
    public ReviewModel $reviews;
    public BoxOptionModel $boxOptions;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->categories = new CategoryModel();
        $this->cart = new CartModel();
        $this->wishlist = new WishlistModel();
        $this->reviews = new ReviewModel();
        $this->boxOptions = new BoxOptionModel();
    }
}

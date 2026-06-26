<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $cart = Cart::create([
            'customer_name' => 'Zacky Dhaffary',
            'status' => 'active',
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => 1,
            'product_name' => 'Keyboard Mechanical',
            'quantity' => 1,
            'price' => 350000,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => 2,
            'product_name' => 'Mouse Wireless',
            'quantity' => 2,
            'price' => 150000,
        ]);
    }
}
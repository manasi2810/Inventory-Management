<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
    [
        'name' => 'A4 Notebook',
        'category_id' => 1,
        'sku' => 'STN-001',
        'description' => 'Ruled A4 size notebook',
        'pack_size' => '1 Piece',
        'moq' => 10,
        'uom' => 'Piece',
        'price' => 30,
        'cost_price' => 20,
        'feature_product' => 1,
        'sequence' => 1,
        'status' => 'active',
        'stock_quantity' => 100,
    ],
    [
        'name' => 'Biscuits Pack',
        'category_id' => 2,
        'sku' => 'FOOD-001',
        'description' => 'Cream biscuits pack',
        'pack_size' => '200g',
        'moq' => 50,
        'uom' => 'Pack',
        'price' => 25,
        'cost_price' => 18,
        'feature_product' => 0,
        'sequence' => 2,
        'status' => 'active',
        'stock_quantity' => 500,
    ],
    [
        'name' => 'Rice Bag 5kg',
        'category_id' => 3,
        'sku' => 'GRY-001',
        'description' => 'Premium basmati rice',
        'pack_size' => '5kg',
        'moq' => 20,
        'uom' => 'Bag',
        'price' => 450,
        'cost_price' => 400,
        'feature_product' => 1,
        'sequence' => 3,
        'status' => 'active',
        'stock_quantity' => 200,
    ],
    [
        'name' => 'Office Chair',
        'category_id' => 4,
        'sku' => 'OFF-001',
        'description' => 'Ergonomic office chair',
        'pack_size' => '1 Unit',
        'moq' => 5,
        'uom' => 'Piece',
        'price' => 2500,
        'cost_price' => 2000,
        'feature_product' => 1,
        'sequence' => 4,
        'status' => 'active',
        'stock_quantity' => 50,
    ],
    [
        'name' => 'Wireless Mouse',
        'category_id' => 5,
        'sku' => 'ELEC-001',
        'description' => 'Bluetooth wireless mouse',
        'pack_size' => '1 Unit',
        'moq' => 10,
        'uom' => 'Piece',
        'price' => 499,
        'cost_price' => 350,
        'feature_product' => 1,
        'sequence' => 5,
        'status' => 'active',
        'stock_quantity' => 300,
    ],
];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
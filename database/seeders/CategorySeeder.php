<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Stationery',
                'description' => 'Office and school stationery items like pens, notebooks, files, etc.',
            ],
            [
                'name' => 'Food Products',
                'description' => 'Packaged and processed food items.',
            ],
            [
                'name' => 'Grocery',
                'description' => 'Daily essential grocery items like rice, pulses, oil, etc.',
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office essentials like chairs, desks, printers, and accessories.',
            ],
            [
                'name' => 'Electronics',
                'description' => 'Electronic goods like mobiles, laptops, chargers, and gadgets.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
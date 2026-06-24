<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

   public function test_product_can_be_created()
    {
        Permission::firstOrCreate([
            'name' => 'product.create'
        ]);

        $user = User::factory()->create();

        $user->givePermissionTo('product.create');

        $category = Category::create([
            'name' => 'Test Category',
            'status' => 'active'
        ]);

        $response = $this->actingAs($user)
            ->post('/Product', [
                'name' => 'HDPE Container',
                'category_id' => $category->id,
                'sku' => 'HDPE001',
                'uom' => 'PCS',
                'price' => 100,
                'status' => 'active'
            ]);

        $response->assertRedirect('/Product');

        $this->assertDatabaseHas('products', [
            'name' => 'HDPE Container'
        ]);
    }
}
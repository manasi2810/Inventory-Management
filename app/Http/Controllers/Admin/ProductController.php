<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Services\ActivityLogService;

class ProductController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;

        $this->middleware('permission:product.view')
            ->only(['index']);

        $this->middleware('permission:product.create')
            ->only(['create', 'store']);

        $this->middleware('permission:product.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:product.delete')
            ->only(['destroy']);
    }


    /**
     * Product Index
     */
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get();

        return view(
            'Admin.Product.index',
            compact('products')
        );
    }


    /**
     * Product Creation Page
     */
    public function create()
    {
        $categories = Category::all();

        return view(
            'Admin.Product.create',
            compact('categories')
        );
    }


    /**
     * Store Product
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $product = Product::create($validated);

        $this->activityLogService->log(
            action: 'created',
            module: 'Product',
            description: 'Product created',
            model: $product,
            newValues: $product->toArray()
        );

        return redirect()
            ->route('Product')
            ->with(
                'success',
                'Product created successfully'
            );
    }


    /**
     * Edit Product
     */
    public function edit(Product $Product)
    {
        $categories = Category::all();

        $product = $Product;

        return view(
            'Admin.Product.edit',
            compact(
                'product',
                'categories'
            )
        );
    }


    /**
     * Update Product
     */
    public function update(
        UpdateProductRequest $request,
        Product $Product
    ) {
        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Old Values
        |--------------------------------------------------------------------------
        */

        $oldValues = $Product->only([
            'name',
            'category_id',
            'sku',
            'description',
            'pack_size',
            'moq',
            'uom',
            'price',
            'cost_price',
            'status',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */

        $Product->update($validated);


        /*
        |--------------------------------------------------------------------------
        | New Values
        |--------------------------------------------------------------------------
        */

        $newValues = $Product
            ->fresh()
            ->only([
                'name',
                'category_id',
                'sku',
                'description',
                'pack_size',
                'moq',
                'uom',
                'price',
                'cost_price',
                'status',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        $this->activityLogService->log(
            action: 'updated',
            module: 'Product',
            description: 'Product updated',
            model: $Product,
            oldValues: $oldValues,
            newValues: $newValues
        );


        return redirect()
            ->route('Product')
            ->with(
                'success',
                'Product updated successfully'
            );
    }


    /**
     * Disable Product
     */
    public function destroy(Product $Product)
    {
        $oldValues = $Product->only([
            'name',
            'category_id',
            'sku',
            'description',
            'pack_size',
            'moq',
            'uom',
            'price',
            'cost_price',
            'feature_product',
            'status',
        ]);


        $Product->update([
            'status' => 'inactive',
        ]);


        $newValues = $Product
            ->fresh()
            ->only([
                'name',
                'category_id',
                'sku',
                'description',
                'pack_size',
                'moq',
                'uom',
                'price',
                'cost_price',
                'feature_product',
                'status',
            ]);


        $this->activityLogService->log(
            action: 'disabled',
            module: 'Product',
            description: 'Product disabled',
            model: $Product,
            oldValues: $oldValues,
            newValues: $newValues
        );


        return redirect()
            ->route('Product')
            ->with(
                'success',
                'Product disabled successfully.'
            );
    }


    /**
     * Restore Product
     */
    public function restore(Product $Product)
    {
        $oldValues = $Product->only([
            'status',
        ]);


        $Product->update([
            'status' => 'active',
        ]);


        $newValues = $Product
            ->fresh()
            ->only([
                'status',
            ]);


        $this->activityLogService->log(
            action: 'restored',
            module: 'Product',
            description: 'Product restored',
            model: $Product,
            oldValues: $oldValues,
            newValues: $newValues
        );


        return redirect()
            ->route('Product')
            ->with(
                'success',
                'Product restored successfully.'
            );
    }
}
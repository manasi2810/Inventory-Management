<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
     
    public function index()
    {
        $products = Product::with('category', 'images')->get();
        return view('admin.Product.index', compact('products'));
    }

     
    public function create()
    {
        $categories = Category::all();
        return view('admin.product.create', compact('categories'));
    }

     
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable',
            'price' => 'nullable|numeric',
            'uom' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            'main_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'description' => 'nullable|string',
            'opening_stock' => 'nullable|integer',
            'pack_size' => 'nullable|string|max:50',
            'moq' => 'nullable|integer',
            'feature_product' => 'nullable|boolean',
            'page_title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255'
        ]);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
    //     // Create product
    //       if ($validator->fails()) {
    //     dd($validator->errors());  
    // }

    // dd($request->all());
        $product = Product::create($request->only([
            'name','category_id','sku','description','opening_stock','pack_size',
            'moq','uom','price','feature_product','status','page_title','alt_text','meta_keywords'
        ]));

        // Save main image
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $product->images()->create([
                'type' => 'main',
                'image_path' => $path
            ]);
        }

        // Save gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'type' => 'gallery',
                    'image_path' =>  $path
                ]);
            }
        }
        return redirect('/Product')
    ->with('success', 'Product created successfully'); 
    }


    // Show edit form
    public function edit(Product $Product)
    {
        $categories = Category::all();
        $product = $Product->load('images');
        return view('admin.Product.edit', compact('product', 'categories'));
    }

    // Update product
   public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'sku' => 'nullable|unique:products,sku,' . $product->id,
        'price' => 'required|numeric',
        'uom' => 'required|string',
        'status' => 'required',
        'main_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    // ✅ SAFE UPDATE
    $product->update([
        'name' => $request->name,
        'category_id' => $request->category_id,
        'sku' => $request->sku,
        'description' => $request->description,
        'opening_stock' => $request->opening_stock,
        'pack_size' => $request->pack_size,
        'moq' => $request->moq,
        'uom' => $request->uom,
        'price' => $request->price,
        'feature_product' => $request->feature_product ?? 0,
        'status' => $request->status,
        'page_title' => $request->page_title,
        'alt_text' => $request->alt_text,
        'meta_keywords' => $request->meta_keywords,
    ]);

    // =========================
    // MAIN IMAGE (ONLY ONCE)
    // =========================
    if ($request->hasFile('main_image')) {

        $oldMain = $product->images()->where('type', 'main')->first();

        if ($oldMain) {
            Storage::disk('public')->delete($oldMain->image_path);
            $oldMain->delete();
        }

        $file = $request->file('main_image');
        $filename = time().'_'.$file->getClientOriginalName();

        $path = $file->storeAs('products', $filename, 'public');

        $product->images()->create([
            'type' => 'main',
            'image_path' => $path
        ]);
    }

    // =========================
    // GALLERY IMAGES (FIXED)
    // =========================
    if ($request->hasFile('gallery_images')) {

        foreach ($request->file('gallery_images') as $image) {

            $filename = time().'_'.$image->getClientOriginalName();
            $path = $image->storeAs('products', $filename, 'public');

            $product->images()->create([
                'type' => 'gallery',
                'image_path' => $path
            ]);
        }
    }
  
        return redirect('/Product')
    ->with('success', 'Category created successfully');
}
    // Delete product
    public function destroy(Product $Product)
    {
        // Delete all related images
        foreach($Product->images as $img){
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        $Product->delete();

        return redirect()->route('Product.index')->with('success','Product deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{   
    public function __construct()
        {
            $this->middleware('permission:product.view')
                ->only([
                    'index'
                ]);

            $this->middleware('permission:product.create')
                ->only([
                    'create',
                    'store'
                ]);

            $this->middleware('permission:product.edit')
                ->only([
                    'edit',
                    'update'
                ]);

            $this->middleware('permission:product.delete')
                ->only([
                    'destroy'
                ]);
        }
        // Product Index
    public function index()
        {
            $products = Product::with('category', 'images')->get();
            return view('Admin.Product.index', compact('products'));
        }

    //  product Creation Page 
    public function create()
        {
            $categories = Category::all();
            return view('Admin.Product.create', compact('categories'));
        }

    //  Store created Product Data 
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
                'pack_size' => 'nullable|string|max:50',
                'moq' => 'nullable|integer',
                'feature_product' => 'nullable|boolean',
                'page_title' => 'nullable|string|max:255',
                'alt_text' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255'
            ]);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
    
            $product = Product::create($request->only([
                'name','category_id','sku','description','pack_size',
                'moq','uom','price','feature_product','status','page_title','alt_text','meta_keywords'
            ]));
            if ($request->hasFile('main_image')) {
                $path = $request->file('main_image')->store('products', 'public');
                $product->images()->create([
                    'type' => 'main',
                    'image_path' => $path
                ]);
            }
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

        // Edit Product
    public function edit(Product $Product)
        {
            $categories = Category::all();
            $product = $Product->load('images');
            return view('Admin.Product.edit', compact('product', 'categories'));
        }
    
        // Update Product
    public function update(Request $request, Product $Product)
            {
                $request->validate([
                'name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'sku' => 'nullable',
                'price' => 'required|numeric',
                'uom' => 'required|string',
                'status' => 'required',
                'main_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            ]); 
            $Product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'sku' => $request->sku,
                'description' => $request->description, 
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
            if ($request->hasFile('main_image')) {
                $oldMain = $Product->images()->where('type', 'main')->first();
                if ($oldMain) {
                    Storage::disk('public')->delete($oldMain->image_path);
                    $oldMain->delete();
                }
                $file = $request->file('main_image');
                $filename = time().'_'.$file->getClientOriginalName();
                $path = $file->storeAs('products', $filename, 'public');
                $Product->images()->create([
                    'type' => 'main',
                    'image_path' => $path
                ]);
            } 
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $filename = time().'_'.$image->getClientOriginalName();
                    $path = $image->storeAs('products', $filename, 'public');
                    $Product->images()->create([
                        'type' => 'gallery',
                        'image_path' => $path
                    ]);
                }
            } 
                return redirect('/Product')
            ->with('success', 'Category created successfully');
        }

    // Delete Product
    public function destroy(Product $Product)
        {
            foreach($Product->images as $img){
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
            $Product->delete();
            return redirect()->route('Product.index')->with('success','Product deleted successfully.');
        }


   
}
<?php

namespace App\Http\Controllers\Admin; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{

    public function __construct()
        {
            // View Permission
            $this->middleware('permission:category.view')
                ->only(['index']);

            // Create Permission
            $this->middleware('permission:category.create')
                ->only(['create', 'store']);

            // Edit Permission
            $this->middleware('permission:category.edit')
                ->only(['edit', 'update']);

            // Delete Permission
            $this->middleware('permission:category.delete')
                ->only(['destroy']);
        }
        // Category index page 

    public function index(Request $request)
        {
            $categories = Category::latest()->get(); 
            return view('Admin.Category.index', compact('categories'));
        }

        // Category cretation
    public function create()
        {
            return view('Admin.Category.create');
        }

        // Store Category
    public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|unique:categories,name'
            ]);
            Category::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return redirect('/Category')
            ->with('success', 'Category created successfully');
        }

        // edit Created Category
    public function edit($id)
        {
            $category = Category::findOrFail($id);
            return view('Admin.Category.edit', compact('category'));
        }

        // Update created Category
    public function update(Request $request, Category $Category)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $Category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('Category')
                ->with('success', 'Category updated successfully!');
        }

        // Delete Category
    public function destroy(Category $category)
        {
            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully');
        }
}
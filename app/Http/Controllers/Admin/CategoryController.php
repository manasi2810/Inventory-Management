<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{
    public function index(Request $request)
{
    $categories = Category::latest()->get(); 
    return view('admin.category.index', compact('categories'));
}

public function create()
    {
        return view('admin.category.create');
    }
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

    public function edit($id)
{
    $category = Category::findOrFail($id);
    return view('admin.category.edit', compact('category'));
}

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
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
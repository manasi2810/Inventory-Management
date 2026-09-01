<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category.view')
            ->only(['index']);

        $this->middleware('permission:category.create')
            ->only(['create', 'store']);

        $this->middleware('permission:category.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:category.delete')
            ->only(['destroy']);
    }

    public function index()
    {
        $categories = Category::latest()->get();

        return view('Admin.Category.index', compact('categories'));
    }

    public function create()
    {
        return view('Admin.Category.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('Category')
            ->with('success', 'Category created successfully');
    }

    public function edit(Category $category)
    {
        return view('Admin.Category.edit', compact('category'));
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {
        $category->update($request->validated());

        return redirect()
            ->route('Category')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('Category')
            ->with('success', 'Category deleted successfully');
    }
}
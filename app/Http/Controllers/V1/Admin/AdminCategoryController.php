<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\V1\CategoryService;
use App\Http\Requests\V1\Admin\StoreCategoryRequest;
use App\Http\Requests\V1\Admin\UpdateCategoryRequest;


class AdminCategoryController extends Controller
{
      protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return new CategoryResource($category);
    }

    
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());
        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}

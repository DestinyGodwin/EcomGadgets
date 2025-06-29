<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{  protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->all();
        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

   
}

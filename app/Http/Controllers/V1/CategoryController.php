<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Services\V1\CategoryService;
use App\Http\Resources\V1\CategoryResource;

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

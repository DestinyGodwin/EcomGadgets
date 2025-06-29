<?php

namespace App\Services\V1;

use App\Models\Category;

class CategoryService
{
    
     public function create(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['name'])) {
            $category->name = $data['name'];
        }
        $category->save();
        return $category;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function all()
    {
        return Category::all();
    }

    public function find(Category $category): Category
    {
        return $category;
    }
}

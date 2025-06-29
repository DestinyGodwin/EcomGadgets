<?php

namespace App\Services\V1\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $store = Auth::user()->store;
            $product = $store->products()->create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'specifications' => $data['specifications'] ?? null,
                'brand' => $data['brand'],
                'price' => $data['price'],
                'wholesale_price' => $data['wholesale_price'],
            ]);

            foreach ($data['images'] as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }

            return $product->load('images', 'store');
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'category_id' => $data['category_id'] ?? $product->category_id,
                'name' => $data['name'] ?? $product->name,
                'description' => $data['description'] ?? $product->description,
                'specifications' => $data['specifications'] ?? $product->specifications,
                'brand' => $data['brand'] ?? $product->brand,
                'price' => $data['price'] ?? $product->price,
                'wholesale_price' => $data['wholesale_price'] ?? $product->wholesale_price,
            ]);

            $remainingImageCount = $product->images()->count();
            $imagesToRemove = $data['removed_images'] ?? [];

            if (!empty($imagesToRemove)) {
                $toDelete = $product->images()->whereIn('id', $imagesToRemove)->get();

                if (($remainingImageCount - $toDelete->count()) + count($data['images'] ?? []) < 1) {
                    throw new \Exception('A product must have at least one image.');
                }

                foreach ($toDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
            if (!empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
            if ($product->images()->count() < 1) {
                throw new \Exception('A product must have at least one image.');
            }

            return $product->load('images', 'store');
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            $product->images()->delete();
            return $product->delete();
        });
    }

    public function getAll()
    {
        return Product::with(['images', 'store'])->latest()->paginate();
    }

   public function getByCategory(string $categoryId, ?string $stateId = null, ?string $lgaId = null)
{
    return Product::with(['images', 'store'])
        ->where('category_id', $categoryId)
        ->whereHas('store', function ($query) use ($stateId, $lgaId) {
            if ($stateId) {
                $query->where('state_id', $stateId);
            }

            if ($lgaId) {
                $query->where('lga_id', $lgaId);
            }
        })
        ->latest()
        ->paginate();
}

    public function getByBrand(string $brand)
    {
        return Product::with(['images', 'store'])
            ->where('brand', $brand)
            ->latest()
            ->paginate();
    }

    public function search(array $filters)
    {
        $query = Product::with(['images', 'store']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'LIKE', '%' . $filters['search'] . '%')
                    ->orWhere('slug', 'LIKE', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand'])) {
            $query->where('brand', $filters['brand']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['sort_by'])) {
            switch ($filters['sort_by']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
            }
        } else {
            $query->latest();
        }

        return $query->paginate();
    }
    public function getByUserState()
    {
        $user = Auth::user();
        return Product::with(['images', 'store'])
            ->whereHas('store', function ($query) use ($user) {
                $query->where('state_id', $user->state_id);
            })
            ->latest()
            ->paginate();
    }

    public function getByUserLga()
    {
        $user = Auth::user();
        return Product::with(['images', 'store'])
            ->whereHas('store', function ($query) use ($user) {
                $query->where('lga_id', $user->lga_id);
            })
            ->latest()
            ->paginate();
    }
    
public function getByState($stateId)
{
    return Product::with(['images', 'store'])
        ->whereHas('store', function ($query) use ($stateId) {
            $query->where('state_id', $stateId);
        })
        ->latest()
        ->paginate();
}

public function getByLga($lgaId)
{
    return Product::with(['images', 'store'])
        ->whereHas('store', function ($query) use ($lgaId) {
            $query->where('lga_id', $lgaId);
        })
        ->latest()
        ->paginate();
}
}

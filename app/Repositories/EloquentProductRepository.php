<?php
namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface {
    
    public function paginate(int $perPage = 20): LengthAwarePaginator {
        return Product::with('category')->orderBy('id','desc')->paginate($perPage);
    }

    public function find(int $id): ?Product { 
        return Product::find($id); 
    }

    public function create(array $data): Product { 
        return Product::create($data); 
    }

    public function update(Product $p, array $data): Product { 
        $p->update($data); return $p->refresh(); 
    }

    public function delete(Product $p): void { 
        $p->delete(); 
    }
}

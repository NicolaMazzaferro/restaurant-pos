<?php
namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface {
    
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::with('category')->orderBy('id', 'desc');

        // Filtro per categoria (se presente)
        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Se l’utente passa ?per_page=all o 0 → restituisci tutti i prodotti
        $requestedPerPage = request('per_page');
        if ($requestedPerPage === 'all' || (is_numeric($requestedPerPage) && (int)$requestedPerPage === 0)) {
            $items = $query->get();
            return new Paginator(
                $items,
                $items->count(), // totale
                $items->count(), // per_page
                1, // current page
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        // Paginazione classica
        $perPage = (int) request('per_page', $perPage);
        return $query->paginate($perPage);
    }

    public function find(int $id): ?Product { 
        return Product::find($id); 
    }

    public function create(array $data): Product { 
        return Product::create($data); 
    }

    public function update(Product $p, array $data): Product { 
        $p->update($data);
        return $p->refresh(); 
    }

    public function delete(Product $p): void { 
        $p->delete(); 
    }
}

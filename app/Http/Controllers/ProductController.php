<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller {

    public function __construct(private ProductRepositoryInterface $repo) {}

    public function index(Request $request)
    {
        $perPageParam = $request->query('per_page');
        $categoryId   = $request->query('category_id');
        $search       = $request->query('search');

        // Base query comune
        $query = Product::with('category')->orderBy('id', 'desc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            // ricerca parziale case-insensitive
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        // Caso: per_page = all oppure 0 → restituisci tutto
        if ($perPageParam === 'all' || (is_numeric($perPageParam) && (int)$perPageParam === 0)) {
            $items = $query->get();

            return response()->json([
                'data' => ProductResource::collection($items),
                'meta' => [
                    'total' => $items->count(),
                    'per_page' => $items->count(),
                    'current_page' => 1,
                    'last_page' => 1,
                ],
            ]);
        }

        // Caso standard: paginazione
        $perPage = is_numeric($perPageParam) ? (int)$perPageParam : 20;

        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $req) {
        $product = $this->repo->create($req->validated());
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function show(int $id) {
        $p = $this->repo->find($id);
        abort_if(!$p, 404, 'Product not found');
        return new ProductResource($p->load('category'));
    }

    public function update(UpdateProductRequest $req, int $id)
    {
        $p = $this->repo->find($id);
        abort_if(!$p, 404, 'Product not found');

        $this->repo->update($p, $req->validated());

        return new ProductResource($p->refresh());
    }

    public function destroy(int $id) {
        $p = $this->repo->find($id);
        abort_if(!$p, 404, 'Product not found');
        $this->repo->delete($p);
        return response()->noContent();
    }
}

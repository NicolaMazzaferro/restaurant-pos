<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller {
    public function __construct(private ProductRepositoryInterface $repo) {}

    public function index()
    {
        $perPageParam = request('per_page');

        // Se il frontend chiede ?per_page=all o 0 → carica tutto
        if ($perPageParam === 'all' || (is_numeric($perPageParam) && (int)$perPageParam === 0)) {
            $query = Product::with('category')->orderBy('id', 'desc');

            if ($categoryId = request('category_id')) {
                $query->where('category_id', $categoryId);
            }

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

        // Altrimenti usa la paginazione classica del repository
        $perPage = is_numeric($perPageParam) ? (int)$perPageParam : 20;

        $products = $this->repo->paginate($perPage);

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

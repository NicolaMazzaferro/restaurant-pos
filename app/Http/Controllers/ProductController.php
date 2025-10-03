<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller {
    public function __construct(private ProductRepositoryInterface $repo) {}

    public function index() {
        return ProductResource::collection($this->repo->paginate());
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

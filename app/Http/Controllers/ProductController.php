<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

/**
 * Controller REST per i Products (solo API JSON).
 * - Delego la business logic a ProductService.
 * - Uso Form Requests per validazione.
 * - Uso API Resources per output coerente.
 */
class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private ProductService $service;

    /**
     * Iniezione del service via costruttore (risolve l'Undefined property).
     */
    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /** GET /api/products */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);

        $query = Product::query()->with('category')->orderBy('id', 'desc');

        // 🔍 Filtro per categoria
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 🔍 Filtro per ricerca testuale
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Se per_page = all → restituisci tutti
        if ($request->get('per_page') === 'all') {
            $items = $query->get();

            return response()->json([
                'data' => ProductResource::collection($items),
                'meta' => [
                    'total' => $items->count(),
                    'message' => 'Products list (all)',
                ],
            ]);
        }

        // Paginazione standard
        $page = $query->paginate($perPage);

        return ProductResource::collection($page)
            ->additional(['meta' => ['message' => 'Products list']]);
    }

    /** POST /api/products (multipart/form-data quando c'è image) */
    public function store(StoreProductRequest $request)
    {

        $data = $request->validated();
        $data['image'] = $request->file('image'); // UploadedFile|null

        $product = $this->service->create($data);

        return (new ProductResource($product))
            ->additional(['meta' => ['message' => 'Product created']])
            ->response()
            ->setStatusCode(201);
    }

    /** GET /api/products/{product} */
    public function show(Product $product)
    {

        return (new ProductResource($product))
            ->additional(['meta' => ['message' => 'Product detail']]);
    }

    /** PUT/PATCH /api/products/{product} */
    public function update(UpdateProductRequest $request, Product $product)
    {

        $data = $request->validated();
        $data['image'] = $request->file('image'); // UploadedFile|null
        if ($request->has('remove_image')) {
            $data['remove_image'] = $request->boolean('remove_image');
        }

        $product = $this->service->update($product, $data);

        return (new ProductResource($product))
            ->additional(['meta' => ['message' => 'Product updated']]);
    }

    /** DELETE /api/products/{product} */
    public function destroy(Product $product)
    {

        $this->service->delete($product);

        return response()->noContent();
    }

}

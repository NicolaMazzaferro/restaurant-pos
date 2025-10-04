<?php

namespace App\Http\Controllers;

use RuntimeException;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /** DI: stesso pattern del ProductController (costruttore con proprietà privata) */
    public function __construct(private CategoryService $service) {}

    /** GET /api/categories */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['search','is_active']);
        $page = $this->service->list($filters, $perPage);

        return CategoryResource::collection($page)
            ->additional(['meta' => ['message' => 'Categories list']]);
    }

    /** POST /api/categories */
    public function store(StoreCategoryRequest $request)
    {
        $cat = $this->service->create($request->validated());

        return (new CategoryResource($cat))
            ->additional(['meta' => ['message' => 'Category created']])
            ->response()
            ->setStatusCode(201);
    }

    /** GET /api/categories/{category} */
    public function show(int $category)
    {
        $cat = $this->service->get($category);

        return (new CategoryResource($cat))
            ->additional(['meta' => ['message' => 'Category detail']]);
    }

    /** PUT/PATCH /api/categories/{category} */
    public function update(UpdateCategoryRequest $request, int $category)
    {
        $cat = $this->service->get($category);

        $cat = $this->service->update($category, $request->validated());

        return (new CategoryResource($cat))
            ->additional(['meta' => ['message' => 'Category updated']]);
    }

    /** DELETE /api/categories/{category} */
    public function destroy(int $category)
    {
        $cat = $this->service->get($category);

        try {
            $this->service->delete($category);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => 'Cannot delete category',
                'errors'  => ['category' => [$e->getMessage()]],
            ], 422);
        }

        return response()->json(['message' => 'Category deleted'], 200);
    }
}

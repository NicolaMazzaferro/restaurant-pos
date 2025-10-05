<?php

namespace App\Http\Controllers;

use Throwable;
use RuntimeException;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Database\QueryException;
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
    public function destroy(int|string $id)
    {
        try {
            $this->service->delete($id);
        } catch (RuntimeException $e) {
            // Categoria con prodotti collegati
            return response()->json([
                'message' => 'Impossibile eliminare la categoria',
                'errors'  => ['category' => [$e->getMessage()]],
            ], 422);
        } catch (QueryException $e) {
            // Violazione FK o errore DB
            return response()->json([
                'message' => 'Errore database durante l\'eliminazione della categoria.',
                'errors'  => ['database' => [$e->getMessage()]],
            ], 500);
        } catch (Throwable $e) {
            // Errore imprevisto
            return response()->json([
                'message' => 'Errore imprevisto durante l\'eliminazione.',
                'errors'  => ['exception' => [$e->getMessage()]],
            ], 500);
        }

        return response()->json(['message' => 'Categoria eliminata con successo.'], 200);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'message' => 'Invalid request. Expected array of IDs.'
            ], 422);
        }

        $deleted = 0;
        $failed = [];

        foreach ($ids as $id) {
            $id = (int) $id; // ✅ conversione esplicita
            if ($id <= 0) continue;

            try {
                $cat = $this->service->get($id);

                if ($cat->products()->exists()) {
                    $failed[] = [
                        'id' => $id,
                        'name' => $cat->name,
                        'reason' => 'Categoria con prodotti associati'
                    ];
                    continue;
                }

                $this->service->delete($id);
                $deleted++;
            } catch (RuntimeException $e) {
                $failed[] = [
                    'id' => $id,
                    'reason' => 'Categoria non trovata'
                ];
            } catch (\Throwable $e) {
                $failed[] = [
                    'id' => $id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Bulk delete completed.',
            'deleted' => $deleted,
            'failed' => $failed,
        ]);
    }


}

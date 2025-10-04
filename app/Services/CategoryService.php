<?php

namespace App\Services;

use RuntimeException;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repo) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function get(int $id): Category
    {
        $cat = $this->repo->findById($id);
        if (!$cat) {
            throw new RuntimeException('Category not found.');
        }
        return $cat;
    }

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug']) && !empty($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $data): Category
    {
        return DB::transaction(function () use ($id, $data) {
            $cat = $this->get($id);
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            return $this->repo->update($cat, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $cat = $this->get($id);
            if ($cat->products()->exists()) {
                throw new RuntimeException('Cannot delete category with related products.');
            }
            $this->repo->delete($cat);
        });
    }
}

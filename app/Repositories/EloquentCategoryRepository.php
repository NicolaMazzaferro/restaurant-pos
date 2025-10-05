<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = Category::query()->withCount('products')->orderBy('sort_order')->orderBy('name');

        if (!empty($filters['search'])) {
            $s = '%'.$filters['search'].'%';
            $q->where(fn($w) => $w->where('name','like',$s)->orWhere('slug','like',$s));
        }
        if (isset($filters['is_active'])) {
            $q->where('is_active', (bool)$filters['is_active']);
        }

        return $q->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function findByIdWithRelations(int $id): ?Category
    {
        return Category::withCount('products')->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}

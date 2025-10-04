<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Product
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\EloquentProductRepository;

// (opzionale) Category, se non l’hai già fatto
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\EloquentCategoryRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);

        // (opzionale) mantieni coerente anche Category
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
    }

    public function boot(): void
    {
        //
    }
}

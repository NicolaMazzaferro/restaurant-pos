<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Repositories\Contracts\ProductRepositoryInterface; // <-- usa l'interfaccia

class ProductService
{
    public function __construct(private ProductRepositoryInterface $repo) {} // <-- type-hint su interface

    /** @param array{name:string,price:float,category_id?:int,stock:int,image?:UploadedFile|null} $payload */
    public function create(array $payload): Product
    {
        return DB::transaction(function () use ($payload) {
            [$data, $image] = $this->extractImage($payload);

            $product = $this->repo->create($data);

            if ($image instanceof UploadedFile) {
                $path = $image->store('products', 'public');
                $product = $this->repo->update($product, [
                    'image_path' => $path,
                    'image_disk' => 'public',
                ]);
            }

            return $product;
        });
    }

    /** @param array{name?:string,price?:float,category_id?:int,stock?:int,image?:UploadedFile|null,remove_image?:bool} $payload */
    public function update(Product $product, array $payload): Product
    {
        return DB::transaction(function () use ($product, $payload) {
            [$data, $image] = $this->extractImage($payload);

            if (!empty($payload['remove_image']) && $product->image_path) {
                Storage::disk($product->image_disk ?: 'public')->delete($product->image_path);
                $data['image_path'] = null;
            }

            $product = $this->repo->update($product, $data);

            if ($image instanceof UploadedFile) {
                if ($product->image_path) {
                    Storage::disk($product->image_disk ?: 'public')->delete($product->image_path);
                }
                $path = $image->store('products', 'public');
                $product = $this->repo->update($product, [
                    'image_path' => $path,
                    'image_disk' => 'public',
                ]);
            }

            return $product;
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if ($product->image_path) {
                Storage::disk($product->image_disk ?: 'public')->delete($product->image_path);
            }
            $this->repo->delete($product);
        });
    }

    /** @return array{0:array,1:UploadedFile|null} */
    private function extractImage(array $payload): array
    {
        $image = $payload['image'] ?? null;
        unset($payload['image']);
        return [$payload, $image];
    }
}

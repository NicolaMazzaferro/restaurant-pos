<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/** Product sold at POS */
class Product extends Model {

    use HasFactory;

    protected $fillable = ['name','category_id','price','stock','image_path','image_disk'];
    protected $casts = ['price' => 'decimal:2'];
    protected $appends = ['image_url'];

    public function category(): BelongsTo { 
        return $this->belongsTo(Category::class); 
    }

    /**
     * Restituisce l'URL pubblico dell'immagine (o null se assente).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        $disk = $this->image_disk ?: config('filesystems.default', 'public');
        return Storage::disk($disk)->url($this->image_path);
    }
}

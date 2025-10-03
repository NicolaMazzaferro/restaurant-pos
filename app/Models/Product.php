<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/** Product sold at POS */
class Product extends Model {

    use HasFactory;

    protected $fillable = ['name','category_id','price','stock'];
    protected $casts = ['price' => 'decimal:2'];

    public function category(): BelongsTo { 
        return $this->belongsTo(Category::class); 
    }
}

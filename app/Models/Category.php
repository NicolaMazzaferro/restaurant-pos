<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Category groups products (e.g., Pizza, Bevande) */
class Category extends Model {
    protected $fillable = ['name'];

    public function products(): HasMany { 
        return $this->hasMany(Product::class); 
    }
}

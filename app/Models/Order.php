<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model {
    protected $fillable = ['user_id','status','type','total'];
    protected $casts = [
        'total'  => 'decimal:2',
        'status' => OrderStatus::class,
        'type'   => OrderType::class,
    ];
    
    public function user(): BelongsTo {
         return $this->belongsTo(User::class); 
    }
    public function items(): HasMany { 
        return $this->hasMany(OrderItem::class); 
    }
    public function receipt(): HasOne { 
        return $this->hasOne(Receipt::class); 
    }
}

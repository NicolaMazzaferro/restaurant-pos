<?php

namespace App\Models;

use App\Models\User;
use App\Models\Receipt;
use App\Enums\OrderType;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model {

    use HasFactory;

    protected $fillable = ['user_id','status','type','total'];

    protected $casts = [
        'status' => OrderStatus::class,
        'type'   => OrderType::class,
        'total'  => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model {
    protected $fillable = ['order_id','total','payment_method','issued_at'];
    protected $casts = [
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
        'payment_method' => PaymentMethod::class
    ];

    public function order(): BelongsTo { 
        return $this->belongsTo(Order::class); 
    }
}

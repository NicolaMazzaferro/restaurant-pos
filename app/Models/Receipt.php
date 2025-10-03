<?php

namespace App\Models;

use App\Models\Order;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model {

    use HasFactory;

    protected $fillable = ['order_id','total','payment_method','issued_at'];
    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'total'     => 'decimal:2',
        'issued_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo { 
        return $this->belongsTo(Order::class); 
    }
}

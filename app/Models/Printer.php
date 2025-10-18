<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Printer
 * Rappresenta la configurazione di una stampante collegata al sistema.
 */
class Printer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'model',
        'header',
        'address',
        'city',
        'phone',
        'vat',
        'printer_port',
    ];
}

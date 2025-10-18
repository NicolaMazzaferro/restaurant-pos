<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Printer
 * Rappresenta la configurazione di una stampante collegata al sistema.
 */
class Printer extends Model
{
    use HasFactory;

    /**
     * Disattiva auto-increment e imposta tipo stringa.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Campi assegnabili in massa.
     */
    protected $fillable = [
        'id',
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

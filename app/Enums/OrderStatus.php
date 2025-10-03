<?php

namespace App\Enums;

/** Order lifecycle for POS */
enum OrderStatus: string {
    case OPEN = 'open';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
}

<?php

namespace App\Enums;

/** Channel of sale (POS scope) */
enum OrderType: string {
    case IN_STORE = 'in_store';   // cassa al banco
    case TAKEAWAY = 'takeaway';   // asporto
}

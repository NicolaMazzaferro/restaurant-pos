<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller {
    // GET /api/orders/{order}/receipt/pdf -> stream PDF (cortesia)
    public function pdf(Order $order) {
        $order->load('items.product','user','receipt');
        abort_if(!$order->receipt, 404, 'Receipt not found (order not paid)');

        $pdf = Pdf::loadView('pdf.receipt', ['order'=>$order]); // view blade per layout PDF
        return $pdf->stream("receipt-{$order->id}.pdf");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Resources\ReceiptResource;
use Illuminate\Http\JsonResponse;  

class ReceiptController extends Controller {
    public function pdf(Order $order) {
        $order->load('items.product','user','receipt');
        abort_if(!$order->receipt, 404, 'Receipt not found (order not paid)');

        $pdf = Pdf::loadView('pdf.receipt', ['order'=>$order]);
        return $pdf->stream("receipt-{$order->id}.pdf");
}

    public function show(Order $order): JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
        {
            $receipt = $order->receipt;

            if (!$receipt) {
                return response()->json([
                    'message' => 'Receipt not found for this order',
                    'errors'  => ['receipt' => ['No receipt has been issued for this order.']],
                ], 404);
            }

            $receipt->loadMissing([
                'order' => function ($q) {
                    $q->with(['items.product']);
                },
            ]);

            return new ReceiptResource($receipt);
    }
}

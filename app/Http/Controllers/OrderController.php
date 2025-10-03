<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller {
    public function __construct(private OrderService $service) {}

    public function store(StoreOrderRequest $req) {
        $order = $this->service->create(
            userId: $req->user()->id,
            items: $req->validated()['items'],
            type: $req->validated()['type'],
            paymentMethod: $req->validated()['payment_method'] ?? null
        );
        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function show(Order $order) {
        return new OrderResource($order->load(['items.product','receipt']));
    }

    public function index() {
        $orders = Order::with('items.product','receipt')->latest()->paginate(20);
        return OrderResource::collection($orders);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePrintersRequest;
use App\Http\Resources\PrinterResource;
use App\Services\PrinterService;
use Illuminate\Http\JsonResponse;

class PrinterController extends Controller
{
    public function __construct(private PrinterService $service) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => PrinterResource::collection($this->service->getAll()),
        ]);
    }

    public function store(SavePrintersRequest $request): JsonResponse
    {
        $this->service->saveAll($request->validated()['printers']);
        return response()->json(['message' => 'Printers saved successfully.']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Exception;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    protected WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $warehouses = $this->warehouseService->getAllWarehouses();

        return sendResponse(
            WarehouseResource::collection($warehouses),
            'Warehouses list',
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        try {
            $warehouse = $this->warehouseService->createWarehouse($request->validated());
            
            return sendResponse(
                new WarehouseResource($warehouse),
                'Warehouse created successfully',
                201
            );
        } catch (Exception $e) {
            // Log the error if needed: Log::error($e->getMessage());
            return sendResponse(
                ['error' => $e->getMessage()],
                'Failed to create warehouse',
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseRequest $request, string $id): JsonResponse
    {
        $warehouse = Warehouse::find($id);

        if (! $warehouse) {
            return sendResponse(null, 'Warehouse not found', 404);
        }

        $updatedWarehouse = $this->warehouseService->updateWarehouse($warehouse, $request->validated());

        return sendResponse(
            new WarehouseResource($updatedWarehouse),
            'Warehouse updated successfully',
            200
        );
    }

    /**
     * Toggle the status of the specified warehouse.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $warehouse = Warehouse::find($id);

        if (! $warehouse) {
            return sendResponse(null, 'Warehouse not found', 404);
        }

        $newStatus = $this->warehouseService->toggleStatus($warehouse);
        $statusText = $newStatus ? 'activated' : 'deactivated';

        return sendResponse(
            ['status' => $newStatus],
            "Warehouse {$statusText} successfully",
            200
        );
    }
}

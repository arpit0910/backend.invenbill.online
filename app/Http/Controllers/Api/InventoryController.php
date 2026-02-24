<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\UpsertInventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function upsert(UpsertInventoryRequest $request, string $productId, string $warehouseId): JsonResponse
    {
        $userId = auth()->id();

        $product = Product::where('created_by', $userId)->where('id', $productId)->first();
        if (! $product) {
            return sendResponse(null, 'Product not found', 404);
        }

        $warehouse = Warehouse::find($warehouseId);
        if (! $warehouse) {
            return sendResponse(null, 'Warehouse not found', 404);
        }

        $data = $request->validated();

        $inventory = Inventory::updateOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $warehouse->id],
            ['quantity' => $data['quantity']]
        );

        return sendResponse($inventory, 'Inventory updated', 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $products = Product::where('created_by', $userId)
            ->with(['category', 'images', 'inventories'])
            ->latest()
            ->get();

        return sendResponse($products, 'Products retrieved successfully', 200);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        $data['created_by'] = auth()->id();
        $data['sku'] = Str::upper(trim($data['sku']));

        $product = Product::create($data)->load(['category', 'images', 'inventories']);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_main' => $index === 0,
                ]);
            }
            $product->load(['images']);
        }

        return sendResponse($product, 'Product created successfully', 201);
    }

    public function show(string $id): JsonResponse
    {
        $userId = auth()->id();
        $product = Product::where('created_by', $userId)
            ->where('id', $id)
            ->with(['category', 'images', 'inventories'])
            ->first();

        if (! $product) {
            return sendResponse(null, 'Product not found', 404);
        }

        return sendResponse($product, 'Product retrieved successfully', 200);
    }

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $userId = auth()->id();
        $product = Product::where('created_by', $userId)->where('id', $id)->first();

        if (! $product) {
            return sendResponse(null, 'Product not found', 404);
        }

        $data = $request->validated();
        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        if (isset($data['sku'])) {
            $data['sku'] = Str::upper(trim($data['sku']));
        }

        $product->update($data);

        return sendResponse($product->load(['category', 'images', 'inventories']), 'Product updated successfully', 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $userId = auth()->id();
        $product = Product::where('created_by', $userId)->where('id', $id)->first();

        if (! $product) {
            return sendResponse(null, 'Product not found', 404);
        }

        // Delete images from storage
        foreach ($product->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $product->delete();

        return sendResponse(null, 'Product deleted successfully', 200);
    }

    public function uploadImages(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $userId = auth()->id();
        $product = Product::where('created_by', $userId)->where('id', $id)->first();

        if (! $product) {
            return sendResponse(null, 'Product not found', 404);
        }

        $created = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $created[] = ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_main' => false,
            ]);
        }

        return sendResponse($created, 'Images uploaded successfully', 201);
    }

    public function setMainImage(string $productId, string $imageId): JsonResponse
    {
        $userId = auth()->id();
        $product = Product::where('created_by', $userId)->where('id', $productId)->first();

        if (! $product) {
            return sendResponse(null, 'Product not found', 404);
        }

        $image = ProductImage::where('product_id', $product->id)->where('id', $imageId)->first();
        if (! $image) {
            return sendResponse(null, 'Image not found for this product', 404);
        }

        ProductImage::where('product_id', $product->id)
            ->where('id', '!=', $image->id)
            ->update(['is_main' => false]);
        $image->is_main = true;
        $image->save();

        return sendResponse(['image_id' => $image->id], 'Main image updated', 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('admin_id', auth()->id())->get();

        return sendResponse(
            CategoryResource::collection($categories),
            'Categories retrieved successfully',
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Trim string inputs
        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        $data['admin_id'] = auth()->id();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return sendResponse(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::where('admin_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (! $category) {
            return sendResponse(null, 'Category not found', 404);
        }

        return sendResponse(
            new CategoryResource($category),
            'Category retrieved successfully',
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = Category::where('admin_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (! $category) {
            return sendResponse(null, 'Category not found', 404);
        }

        $data = $request->validated();

        // Trim string inputs
        array_walk($data, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });
        
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return sendResponse(
            new CategoryResource($category),
            'Category updated successfully',
            200
        );
    }

    /**
     * Toggle the status of the specified category.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $category = Category::where('admin_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (! $category) {
            return sendResponse(null, 'Category not found', 404);
        }

        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();

        $statusText = $category->status === 'active' ? 'activated' : 'deactivated';

        return sendResponse(
            ['status' => $category->status],
            "Category {$statusText} successfully",
            200
        );
    }
}

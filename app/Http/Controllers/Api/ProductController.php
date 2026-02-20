<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::with('category')->paginate(10)
        );
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);
        $product = Product::create($validated);
        $product->load('category');

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        $product->load('category', 'orders');

        return new ProductResource($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);
        $product->update($validated);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil diupdate',
            'data' => $product,
        ]);
    }

    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil dihapus',
        ], 200);
    }
}

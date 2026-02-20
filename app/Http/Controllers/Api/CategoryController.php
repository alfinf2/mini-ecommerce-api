<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->paginate(10);
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = Category::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Category berhasil dibuat',
            'data' => $category,
        ], 201);
    }

    public function show(Category $category)
    {
        $category->load('products');
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Category berhasil diupdate',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category berhasil dihapus',
        ], 200);
    }
}
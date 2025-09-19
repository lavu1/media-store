<?php

namespace App\Http\Controllers;


use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = Category::create($validated);
        return response()->json($category, Response::HTTP_CREATED);
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255'
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

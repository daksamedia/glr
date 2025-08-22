<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'icon' => $category->icon,
                'created_at' => $category->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'payload' => $categories
        ]);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'success' => true,
            'payload' => [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                'description' => $category->description,
            ]
        ]);
    }
}
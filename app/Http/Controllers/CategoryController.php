<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
 public function index()
    {
        $categories = Category::all()->map(function ($category) {
            if ($category->image_path && !str_starts_with($category->image_path, 'http')) {
              $category->image_path= $category->image_path = asset('storage/' . $category->image_path);
            }
            return $category;
        });
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|unique:category,name|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');
        }

        $category = Category::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_path'    => $path,
        ]);

        return response()->json([
            'message'  => 'Thêm danh mục thành công!',
            'category' => $category,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|unique:category,name,' . $id . '|max:150',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $category = Category::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }

            $file = $request->file('image');
            $fileName =$file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');
            $category->image_path = $path;
        }

        $category->name = $validated['name'];
        $category->description = $validated['description'] ?? $category->description;
        $category->save();

        return response()->json([
            'message'  => 'Cập nhật danh mục thành công!',
            'category' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return response()->json(['message' => 'Xóa danh mục thành công!']);
    }

    public function getProducts($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

$products = $category->products->map(function ($product) {
            if ($product->image_path && !str_starts_with($product->image_path, 'http')) {
              $product->image_path= $product->image_path = asset('storage/' . $product->image_path);
            }
            return $product;
        });

        return response()->json($products);
    }

}

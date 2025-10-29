<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
public function index(){
    $categories = Category::all();
    return response()->json($categories);
   }


   public function store(Request $request){
  $validated = $request->validate([
        'name'        => 'required|unique:category,name|max:255',
        'description' => 'nullable|string',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);
$url = null;
    $path = null;
    if ($request->hasFile('image')) {
    $file = $request->file('image');
    $fileName = $file->getClientOriginalName(); //giữ nguyên tên file
    $path = $file->storeAs('images', $fileName, 'public');
    $url = $path ? asset('storage/' . $path) : null;
}
    $category = Category::create([
        'name'        => $validated['name'],
        'description' => $validated['description'] ?? null,
        'image_url'   => $url
    ]);

    return response()->json([
        'message'  => 'Thêm sản phẩm thành công!',
        'category' => $category
    ], 201);

   }


   public function update(Request $request, $id)
{
    // Validate dữ liệu
    $validated = $request->validate([
        'name'        => 'required|unique:category,name,' . $id . '|max:150',
        'description' => 'nullable|string',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Lấy category theo id
    $category = Category::findOrFail($id);

    // Nếu có file ảnh mới
    if ($request->hasFile('image')) {
        // Xóa ảnh cũ nếu có
        if ($category->image_url) {
            $oldPath = str_replace(url('storage') . '/', '', $category->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('image');
        $fileName = $file->getClientOriginalName();  
        $path = $file->storeAs('images', $fileName, 'public');
        $category->image_url = asset('storage/' . $path);
    }
   

    // Cập nhật các trường còn lại
    $category->name = $validated['name'];
    $category->description = $validated['description'] ?? $category->description;

    // Lưu dữ liệu
    $category->save();

    return response()->json([
        'message'  => 'Cập nhật danh mục thành công!',
        'category' => $category
    ]);
}

public function destroy($id)
{
    $category = Category::findOrFail($id);

    // Xóa ảnh nếu có
    if ($category->image_url) {
        $oldPath = str_replace(url('storage') . '/', '', $category->image_url);
        Storage::disk('public')->delete($oldPath);
    }

    $category->delete();

    return response()->json([
        'message' => 'Xóa danh mục thành công!'
    ]);

}

public function getProducts($categoryId)
{
    $category = Category::find($categoryId);

    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    $products = $category->products; // Quan hệ hasMany

    return response()->json($products);
}


public function getProductCategoryName($categoryName)
{
     $category = Category::where('name', $categoryName)->first();

    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    $products = $category->products; // Quan hệ hasMany

    return response()->json($products);
}
}

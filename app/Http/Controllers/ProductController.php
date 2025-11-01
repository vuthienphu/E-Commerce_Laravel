<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Enums\SortType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|unique:products,name|max:150',
            'description' => 'nullable|string',
             'size' => ['nullable', Rule::in(['S','M','L','XL','XXL','XS'])],
            'original_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:category,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'countRate' => 'nullable|numeric',
            
        ]);

$validated['countRate'] = $validated['countRate'] ?? 0;

 $path = null;
    if ($request->hasFile('image')) {
    $file = $request->file('image');
    $fileName = $file->getClientOriginalName(); //giữ nguyên tên file
    $path = $file->storeAs('images', $fileName, 'public');
    }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'size' => $validated['size'] ?? null,
            'original_price' => $validated['original_price'] ?? 0,
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'quantity' => $validated['quantity'] ?? 0,
            'category_id' => $validated['category_id'],
            'image_path'   => $path,
           'countRate' => $validated['countRate'],
        ]);

        return response()->json([
            'message' => 'Thêm sản phẩm thành công!',
            'product' => $product
        ], 201);
        
    }

    public function index(){
      $products = Product::all()->map(function ($product) {
            if ($product->image_path && !str_starts_with($product->image_path, 'http')) {
              $product->image_path= $product->image_path = asset('storage/' . $product->image_path);
            }
            return $product;
        });
        return response()->json($products);
    }

    public function show($id){
        $product = Product::findOrFail($id);
         if ($product->image_path && !str_starts_with($product->image_path, 'http')) {
        $product->image_path = asset('storage/' . $product->image_path);
    }
           return response()->json($product);
    }

    public function search(Request $request){
$keyword = $request -> input('keyword');
$size = $request->input('size');
$sort = $request->query('sort', SortType::PRICE_ASC->value);
$minDisCount = $request->input('discount_percent',null);
$perPage = $request->input('per_page', 6);

$query = Product::query()
        ->join('category', 'category.id', '=', 'products.category_id')
        ->select('products.*'); // tránh lỗi khi join

    if (!empty($keyword)) {
        $query->where(function ($q) use ($keyword) {
            $q->where('products.name', 'LIKE', "%{$keyword}%")
              ->orWhere('products.description', 'LIKE', "%{$keyword}%")
              ->orWhere('category.name', 'LIKE', "%{$keyword}%");
        });
    }

    if(!empty($size) && is_array($size)){
        $query -> whereIn('size',$size);
    }


    if($minDisCount !== null && $minDisCount !== ''){
        $query -> where('discount_percent','>=',$minDisCount);
    }

     switch (SortType::from($sort)) {
        case SortType::PRICE_ASC:
            $query->orderByRaw('(original_price * (1 - discount_percent / 100)) asc');
            break;

        case SortType::PRICE_DESC:
            $query->orderByRaw('(original_price * (1 - discount_percent / 100)) desc');
            break;

    }

   
    $products = $query->get()->map(function ($product) {
    if ($product->image_path && !str_starts_with($product->image_path, 'http')) {
        $product->image_path = asset('storage/' . $product->image_path);
    }
    return $product;
});
    
    //$products = $query -> paginate($perPage);
    return response()->json($products);

    }

    public function update(Request $request, $id)
{
    // Validate dữ liệu
    $validated = $request->validate([
        'name'        => 'required|unique:products,name,' . $id . '|max:255',
        'description' => 'nullable|string',
        'size' => ['nullable', Rule::in(['S','M','L','XL','XXL','XS'])],
            'original_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:category,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'countRate' => 'nullable|numeric',
    ]);

    // Lấy category theo id
    $product = Product::findOrFail($id);

    // Nếu có file ảnh mới
    if ($request->hasFile('image')) {
        // Xóa ảnh cũ nếu có
        if ($product->image_path) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
        }

        $file = $request->file('image');
        $fileName = $file->getClientOriginalName(); 
        $path = $file->storeAs('images', $fileName, 'public');
        $product->image_path = $path;
    }
   

    // Cập nhật các trường còn lại
    $product->name = $validated['name'];
    $product->description = $validated['description'] ?? $product->description;
    $product->size = $validated['size'] ?? $product->size;
    $product->original_price = $validated['original_price'] ?? $product->original_price;
    $product->discount_percent = $validated['discount_percent'] ?? $product->discount_percent;
    $product->quantity = $validated['quantity'] ?? $product->quantity;
    $product->category_id = $validated['category_id'] ?? $product->category_id;
    $product->countRate = $validated['countRate'] ?? $product->countRate;    

    // Lưu dữ liệu
    $product->save();

    return response()->json([
        'message'  => 'Cập nhật sản phẩm thành công!',
        'product' => $product
    ]);
}

public function destroy($id)
{
    $product = Product::findOrFail($id);

    // Xóa ảnh nếu có
    if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }


    $product->delete();

    return response()->json([
        'message' => 'Xóa danh mục thành công!'
    ]);

}



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;
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

 $path = null;
 $url = null;
    if ($request->hasFile('image')) {
    $file = $request->file('image');
    $fileName = $file->getClientOriginalName(); //giữ nguyên tên file
    $path = $file->storeAs('images', $fileName, 'public');
    $url = $path ? asset('storage/' . $path) : null;
    }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'size' => $validated['size'] ?? null,
            'original_price' => $validated['original_price'] ?? 0,
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'quantity' => $validated['quantity'] ?? 0,
            'category_id' => $validated['category_id'],
            'image_url'   => $url,
            'countRate' => $validated['countRate']
        ]);

        return response()->json([
            'message' => 'Thêm sản phẩm thành công!',
            'product' => $product
        ], 201);
        
    }

    public function index(){
        $products = Product::all();
       return response() ->json($products);
    }

    public function show($id){
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function search(Request $request){
$keyword = $request -> input('keyword');
$size = $request->input('size');
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
    $products= $query->get();
    $products = $query -> paginate($perPage);
    return response()->json($products);


    }
}

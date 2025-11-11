<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller;
use App\Models\Cart;
use App\Models\CartItem;

use Illuminate\Http\Request;

class CartController extends Controller
{
     public function store(Request $request){
$validate = $request->validate([
    'id'=>'required|integer|exists:users,id',
]);

     $cart = Cart::firstOrCreate(['id'=>$validate['id'],
                                ]);

    return response()->json([
        'message'=>'Tạo giỏ hàng thành công',
        'cart'=> $cart
    ],201);
    }
    public function getToTalPrice($id){
        $totalPrice = 0;
        $cart = Cart::findOrFail($id);
        $products = $cart->items;
        foreach($products as $product){
            $totalPrice += $product->getPrice();
        }
        return response()->json(["products"=>$products,
                                "totalPrice"=>$totalPrice]);
    }

    
}
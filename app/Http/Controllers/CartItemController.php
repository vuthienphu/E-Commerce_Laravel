<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Response;

class CartItemController extends Controller
{
    public function store(Request $request){
$validate = $request->validate([
    'cart_id'=>'required|integer|exists:carts,id',
    'product_id'=>'required|integer|exists:products,id',
    'quantity'=>'required|integer|min:1']);

     $cartItem = CartItem::create(['cart_id'=>$validate['cart_id'],
                                    'product_id'=>$validate['product_id'],
                                    'quantity'=>$validate['quantity']
                                ]);

    return response()->json([
        'message'=>'Thêm sản phẩm vào giỏ hàng thành công',
        'cartItem'=> $cartItem
    ],201);
    }

   public function update($id,Request $request){
    $validate = $request->validate([
    'cart_id'=>'required|integer|exists:carts,id',
    'product_id'=>'required|integer|exists:products,id',
    'quantity'=>'required|integer|min:1']);

  $cartItem = CartItem::findOrFail($id);

   $cartItem -> cart_id = $validate['cart_id'];
    $cartItem -> product_id = $validate['product_id'];
    $cartItem -> quantity = $validate['quantity'];

    $cartItem -> save();
    return response()->json("Cap nhat gio hang thanh cong");
   }


    public function destroy($id){
      $cartItem = CartItem::findOrFail($id);
        $cartItem -> delete();
        return response() -> json("Xoa san pham trong gio thanh cong");
    }
}

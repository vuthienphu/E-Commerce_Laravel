<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
public function storeBuyNow(Request $request){
$validateOrder = $request->validate(['user_id'=>'required|integer|exists:users,id',]);
$order = Order::create(['user_id'=>$validateOrder['user_id']]);
$orderId = $order->id;
$validateOrderItem = $request -> validate(['product_id'=>'required|integer|exists:products,id',
                                            'quantity'=>'integer|min:1']);
$orderItem = OrderItem::create(['order_id'   => $orderId,
    'product_id'=>$validateOrderItem['product_id'],
                                'quantity'=> $validateOrderItem['quantity']]);
$orderItem->product_name = $orderItem->product->name;
$orderItem->original_price = $orderItem->product->original_price;
$orderItem->discount_percent = $orderItem->product->discount_percent;
$orderItem->subtotal = $orderItem->original_price * (1 - $orderItem->discount_percent/ 100) * $orderItem->quantity;
$orderItem->save();

$order->items_count = 1;
$order->total_price = $orderItem->subtotal;
$order->save();

return response()->json([
    'message'=>"Tạo đơn hàng thành công",
    'order'=>$order
],201);
}

public function storeCart(Request $request){
    $validateOrder = $request->validate(['user_id'=>'required|integer|exists:users,id',]);
    
    $order = Order::create(['user_id'=>$validateOrder['user_id']]);
    $cart = Cart::findOrFail($order->user_id);
    $products = $cart->items;
    $orderId = $order->id;
    foreach($products as $product){
        $orderItem = OrderItem::create((['order_id'   => $orderId,
                                        'product_id'=>$product->product_id,
                                        'quantity'=> $product->quantity,]));
    $orderItem->product_name = $orderItem->product->name;
$orderItem->original_price = $orderItem->product->original_price;
$orderItem->discount_percent = $orderItem->product->discount_percent;
$orderItem->subtotal = $orderItem->original_price * (1 - $orderItem->discount_percent/ 100) * $orderItem->quantity;
$orderItem->save();
    }
  
$order->items_count = count($products);
$totalPrice = 0;
        foreach($products as $product){
            $totalPrice += $product->getPrice();
        }
$order->total_price = $totalPrice;
$order->save();
    return response()->json([
    'message'=>"Tạo đơn hàng thành công",
    'order'=>$order
],201);
}

}

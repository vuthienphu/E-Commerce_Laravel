<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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
}

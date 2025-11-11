<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * Quan hệ: cart item thuộc về 1 cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Quan hệ: cart item thuộc về 1 sản phẩm
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPrice(){
        return $this->product->original_price * (1 - $this->product->discount_percent / 100)* $this->quantity;
    }
}

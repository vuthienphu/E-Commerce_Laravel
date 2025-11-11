<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
     use HasFactory;

    // Vì cột id không phải auto-increment mà là foreign key của user_id
    protected $primaryKey = 'id';
    public $incrementing = false; // tắt tự tăng id
    protected $keyType = 'int';   // kiểu khóa chính là integer

    protected $fillable = [
        'id',
    ];

    /**
     * Quan hệ: 1 cart thuộc về 1 user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    /**
     * Quan hệ: 1 cart có nhiều cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}

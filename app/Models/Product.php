<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

      protected $table = 'products';

    // Các cột có thể gán giá trị hàng loạt (mass assignable)
    protected $fillable = [
        'name',
        'description',
        'size',
        'original_price',
        'discount_percent',
        'quantity',
        'category_id',
        'image_url',
        'countRate',
    ];

    /**
     * Quan hệ: Mỗi sản phẩm thuộc về 1 danh mục
     */
    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Tính giá sau khi giảm tự động
     */
    public function getFinalPriceAttribute()
    {
        return $this->original_price * (1 - $this->discount_percent / 100);
    }
}

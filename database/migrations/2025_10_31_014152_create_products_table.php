<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // Tên sản phẩm
            $table->text('description')->nullable(); // Mô tả
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL', 'XXL'])->nullable();
            $table->decimal('original_price', 10, 2)->default(0); // Giá gốc
            $table->decimal('discount_percent', 5, 2)->default(0); // % giảm giá
            $table->integer('quantity')->default(0); // Số lượng tồn
            $table->unsignedBigInteger('category_id'); // Danh mục ID
            $table->string('image_path')->nullable(); // Ảnh sản phẩm
            $table->integer('countRate')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

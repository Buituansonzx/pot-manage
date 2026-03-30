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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id')->comment('Liên kết tới bảng products');
            $table->string('image_url')->comment('Đường dẫn ảnh');
            $table->boolean('is_primary')->default(false)->comment('Ảnh đại diện cho sản phẩm');
            $table->integer('sort_order')->default(0)->comment('Thứ tự hiển thị ảnh');
            
            $table->timestamps();

            // Định nghĩa khóa ngoại
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};

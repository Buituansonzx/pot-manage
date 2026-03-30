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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id')->comment('Liên kết tới bảng products');
            $table->string('attribute_name')->comment('Ví dụ: Đường kính, Chiều cao, Màu sắc');
            $table->string('attribute_value')->comment('Ví dụ: 20cm, 30cm, Men xanh');
            
            $table->timestamps();

            // Set khóa ngoại liên kết tới products, cascade xóa
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
        Schema::dropIfExists('product_attributes');
    }
};

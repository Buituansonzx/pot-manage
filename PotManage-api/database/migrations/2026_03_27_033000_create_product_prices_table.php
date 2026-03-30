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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('Khóa ngoại liên kết với bảng products');
            $table->decimal('floor_price', 15, 2)->comment('Giá sàn thu về (cho CTV)');
            $table->decimal('suggested_retail_price', 15, 2)->comment('Giá bán lẻ đề xuất tại cửa hàng');
            $table->decimal('min_retail_price', 15, 2)->nullable()->comment('Giá tối thiểu nhân viên được phép giảm');
            
            $table->timestamps(); 
            
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
        Schema::dropIfExists('product_prices');
    }
};

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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id')->comment('Liên kết với sản phẩm');
            $table->string('name')->comment('Tên lô hàng (VD: LÔ-TQ-MARCH-2026)');
            $table->integer('quantity_imported')->comment('Số lượng nhập');
            $table->integer('quantity_remaining')->comment('Số lượng còn lại');
            $table->decimal('unit_cost_yuan', 15, 2)->comment('Giá tệ gốc tại xưởng');
            $table->decimal('unit_cost_vnd', 15, 2)->comment('Giá vốn sau khi quy đổi và tính phí ship');
            
            $table->timestamps();

            // Khóa ngoại tới products
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
        Schema::dropIfExists('product_batches');
    }
};

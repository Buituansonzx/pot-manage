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
            $table->unsignedBigInteger('category_id')->comment('Phân loại: Chậu gốm, Chậu nhựa, Đôn...');
            $table->string('sku')->unique()->comment('Mã định danh duy nhất cho sản phẩm');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('thumbnail')->nullable();
            $table->text('description')->nullable();
            $table->string('unit')->default('cái')->comment('Đơn vị tính');
            $table->boolean('status')->default(true)->comment('Trạng thái kích hoạt');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
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

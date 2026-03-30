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
        Schema::create('categories', function (Blueprint $table) {
            $table->id()->comment('Mã định danh duy nhất (Khóa chính)');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('ID của danh mục cha (dùng để phân cấp thư mục)');
            $table->string('name')->comment('Tên của danh mục');
            $table->string('description')->nullable()->comment('Mô tả chi tiết về danh mục');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

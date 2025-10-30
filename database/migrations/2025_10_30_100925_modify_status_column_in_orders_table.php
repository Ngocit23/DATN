<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Sửa kiểu dữ liệu của cột 'status' thành ENUM
        $table->enum('status', ['Đang xử lý', 'Hoàn thành', 'Đã hủy'])->default('Đang xử lý')->change();
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        // Quay lại kiểu dữ liệu cũ (TINYINT)
        $table->tinyInteger('status')->default(0)->change();
    });
}

};

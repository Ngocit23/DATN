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
        Schema::table('order_details', function (Blueprint $table) {
            // Thay đổi cột 'status' thành ENUM với các giá trị cụ thể
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
    
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Quay lại kiểu dữ liệu cũ (nếu rollback migration)
            $table->tinyInteger('status')->default(0)->change();
        });
    }
    
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tăng độ dài cột status lên 50 (đủ cho tiếng Việt có dấu)
            $table->string('status', 50)->change();
            
            // Tương tự cho payment_status (nếu cần)
            $table->string('payment_status', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->change(); // quay lại độ dài cũ
            $table->string('payment_status', 20)->change();
        });
    }
};
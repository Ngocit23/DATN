<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Tăng độ dài cột status lên 20 (đủ cho 'confirmed', 'pending', 'cancelled',...)
            $table->string('status', 20)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('status', 10)->default('pending')->change(); // quay lại độ dài cũ
        });
    }
};
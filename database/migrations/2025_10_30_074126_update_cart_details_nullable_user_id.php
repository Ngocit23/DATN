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
    Schema::table('cart_details', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->change();  // Làm cho user_id có thể null
    });
}

public function down()
{
    Schema::table('cart_details', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable(false)->change(); // Đảm bảo khi rollback sẽ trở lại không null
    });
}

};

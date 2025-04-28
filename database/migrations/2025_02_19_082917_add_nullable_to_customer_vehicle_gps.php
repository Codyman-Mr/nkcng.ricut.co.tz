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
    Schema::table('customer_vehicle_gps', function (Blueprint $table) {
        $table->bigInteger('user_id')->unsigned()->nullable()->change();
        $table->bigInteger('customer_vehicle_id')->unsigned()->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_vehicle_gps', function (Blueprint $table) {
            //
        });
    }
};

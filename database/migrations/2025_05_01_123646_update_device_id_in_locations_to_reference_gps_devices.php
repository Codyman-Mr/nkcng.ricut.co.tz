<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Make device_id nullable
            $table->unsignedBigInteger('device_id')->nullable()->change();

            // Add foreign key
            $table->foreign('device_id')->references('id')->on('gps_devices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->unsignedBigInteger('device_id')->notNullable()->change();
        });
    }
};
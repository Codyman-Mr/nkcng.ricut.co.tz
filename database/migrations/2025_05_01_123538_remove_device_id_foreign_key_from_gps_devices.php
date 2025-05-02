<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_devices', function (Blueprint $table) {
            $table->dropForeign(['device_id']); // Remove foreign key to locations.id
        });
    }

    public function down(): void
    {
        Schema::table('gps_devices', function (Blueprint $table) {
            $table->foreign('device_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }
};

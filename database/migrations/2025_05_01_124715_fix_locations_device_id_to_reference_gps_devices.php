<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('locations', function (Blueprint $table) {
        //     // Drop the unique constraint
        //     // $table->dropUnique(['device_id']);
            
        //     // Change device_id to unsignedBigInteger and make it nullable
        //     $table->unsignedBigInteger('device_id')->nullable()->change();
            
        //     // Add foreign key to gps_devices.id
        //     $table->foreign('device_id')->references('id')->on('gps_devices')->onDelete('set null');
        // });
    }

    public function down(): void
    {
        // Schema::table('locations', function (Blueprint $table) {
        //     $table->dropForeign(['device_id']);
        //     $table->string('device_id', 255)->notNullable()->change();
        //     $table->unique('device_id');
        // });
    }
};

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
        Schema::table('gps_devices', function (Blueprint $table) {
            $table->index('assignment_status');
            $table->index('assigned_to');
            $table->index('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gps_devices', function (Blueprint $table) {
            $table->dropIndex(['assignment_status']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['vehicle_id']);
        });
    }
};

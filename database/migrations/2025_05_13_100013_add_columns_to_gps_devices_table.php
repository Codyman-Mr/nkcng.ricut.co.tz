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
            // Drop the existing user_id foreign key and column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add new columns
            $table->enum('activity_status', ['active', 'inactive'])->default('inactive')->after('device_id');
            $table->enum('assignment_status', ['assigned', 'unassigned'])->default('unassigned')->after('activity_status');
            $table->foreignId('assigned_to')->nullable()->after('assignment_status')->constrained('users')->onDelete('set null');
            $table->string('assigned_by')->nullable()->after('assigned_to');
            $table->dateTime('assigned_at')->nullable()->after('assigned_by');
            $table->dateTime('unassigned_at')->nullable()->after('assigned_at');
            $table->string('unassigned_by')->nullable()->after('unassigned_at');
            $table->text('unassigned_reason')->nullable()->after('unassigned_by');

            // Add columns for vehicle power control
            $table->enum('power_status', ['on', 'off'])->default('off')->after('unassigned_reason');
            $table->dateTime('power_status_updated_at')->nullable()->after('power_status');
            $table->text('power_status_notes')->nullable()->after('power_status_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gps_devices', function (Blueprint $table) {
            // Restore user_id column and foreign key
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Drop new columns
            $table->dropColumn([
                'activity_status',
                'assignment_status',
                'assigned_to',
                'assigned_by',
                'assigned_at',
                'unassigned_at',
                'unassigned_by',
                'unassigned_reason',
                'power_status',
                'power_status_updated_at',
                'power_status_notes'
            ]);
        });
    }
};

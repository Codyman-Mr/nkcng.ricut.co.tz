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
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_vehicle_id')->constrained('customer_vehicles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('cylinder_type_id')->constrained('cylinder_types')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['pending', 'completed']);
            $table->enum('payment_type', ['direct', 'loan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installations');
    }
};

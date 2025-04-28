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

            Schema::create('customer_vehicle_gps', function (Blueprint $table) {
                $table->string('imei', 20)->primary(); // Use IMEI as primary key
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->float('lat')->nullable();
                $table->float('lng')->nullable();
                $table->integer('speed')->nullable();
                $table->boolean('on_status')->default(true);
                $table->timestamps();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_vehicle_gps');
    }
};

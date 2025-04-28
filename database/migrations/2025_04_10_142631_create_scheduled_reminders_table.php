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
         Schema::create('scheduled_reminders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->date('due_date'); // Next expected payment
        $table->dateTime('scheduled_at'); // When reminder should be sent
        $table->text('message')->nullable(); // Optional custom message
        $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
        $table->text('error_message')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reminders');
    }
};

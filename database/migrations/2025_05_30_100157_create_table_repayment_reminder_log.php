<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repayment_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete(); // Foreign key to loans table
            $table->string('type'); // e.g. 'before', 'on', 'after'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayment_reminder_logs');
    }
};

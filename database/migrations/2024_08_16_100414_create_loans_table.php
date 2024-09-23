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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('installation_id')->constrained('installations')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('loan_type', ['NK CNG Automotive Loan', 'Maendeleo Bank Loan'])->default('NK CNG Automotive Loan');
            $table->decimal('loan_required_amount', 10, 2)->default(0);
            $table->enum('loan_payment_plan', ['weekly', 'bi-weekly', 'monthly'])->default('weekly');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

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
        Schema::table('loan_packages', function (Blueprint $table) {
            $table->dropColumn('cylinder_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_packages', function (Blueprint $table) {
            $table->string('cylinder_capacity')->after('amount_to_finance');
        });
    }
};

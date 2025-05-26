<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cylinder_types', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_package_id')->nullable()->after('name');
            $table->foreign('loan_package_id')
                ->references('id')->on('loan_packages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('capacity', 10, 2)->after('name');

        });
    }

    public function down(): void
    {
        Schema::table('cylinder_types', function (Blueprint $table) {
            $table->dropForeign(['loan_package_id']);
            $table->dropColumn(['loan_package_id', 'capacity', 'deleted_at']);
        });
    }
};

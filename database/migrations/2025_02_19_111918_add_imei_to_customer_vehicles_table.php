<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('customer_vehicles', function (Blueprint $table) {
        $table->string('imei', 20)->unique()->after('id')->nullable(true);
    });
}

public function down()
{
    Schema::table('customer_vehicles', function (Blueprint $table) {
        $table->dropColumn('imei');
    });
}
};

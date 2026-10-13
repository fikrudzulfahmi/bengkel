<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop service_fee since we will use transaction_services table
            $table->dropColumn('service_fee');
            $table->integer('discount')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('service_fee')->default(0);
            $table->dropColumn('discount');
        });
    }
};

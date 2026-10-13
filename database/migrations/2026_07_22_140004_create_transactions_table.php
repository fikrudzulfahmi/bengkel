<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // mekanik
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('motorcycle_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('service_fee')->default(0); // Biaya Jasa Servis
            $table->integer('total_price')->default(0); // Total biaya (Sparepart + Jasa)
            $table->string('status')->default('selesai'); // pending, selesai
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

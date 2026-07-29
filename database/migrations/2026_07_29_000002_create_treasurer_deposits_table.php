<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasurer_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mechanic_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('treasurer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('amount');
            $table->text('description');
            $table->date('date');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_note')->nullable();
            // FK ke cash_books setelah di-approve
            $table->foreignId('mechanic_cash_book_id')->nullable()->constrained('cash_books')->nullOnDelete();
            $table->foreignId('treasurer_cash_book_id')->nullable()->constrained('cash_books')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasurer_deposits');
    }
};

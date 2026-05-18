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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')->constrained('currencies')->onDelete('cascade');
            $table->foreignId('to_currency_id')->constrained('currencies')->onDelete('cascade');
            $table->decimal('rate', 18, 8); // Курс обмена
            $table->date('date'); // Дата курса
            $table->timestamps();
            
            // Уникальная комбинация - один курс на дату
            $table->unique(['from_currency_id', 'to_currency_id', 'date']);
            
            // Индексы для поиска
            $table->index(['from_currency_id', 'date']);
            $table->index(['to_currency_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};

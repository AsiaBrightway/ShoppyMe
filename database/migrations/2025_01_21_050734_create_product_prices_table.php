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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id('product_price_id');
            $table->integer('product_id');
            $table->integer('size_id');
            $table->integer('color_id');
            $table->string('price', 191);
            $table->string('stock_qty', 191);
            $table->string('return_points', 191);
            $table->boolean('is_promotion');
            $table->string('promotion_price', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
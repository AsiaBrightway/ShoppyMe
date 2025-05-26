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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->integer('profile_id');
            $table->string('sub_total', 191);
            $table->string('delivery_charges', 191);
            $table->string('total', 191);
            $table->string('remark', 191)->nullable();
            $table->boolean('need_to_confirm')->nullable();
            $table->boolean('is_paid')->nullable();
            $table->boolean('is_confirmed')->nullable();
            $table->boolean('is_shipped')->nullable();
            $table->boolean('is_delivered')->nullable();
            $table->boolean('is_cancelled')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

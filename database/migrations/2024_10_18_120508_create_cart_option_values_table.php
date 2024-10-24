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
        Schema::create('cart_option_values', function (Blueprint $table) {
            $table->uuid('cart_id');
            $table->foreign('cart_id')
                ->references('id')->on('carts')
                ->cascadeOnDelete();
            $table->foreignId('option_value_id')
                ->constrained('option_values');
            $table->unique(['cart_id', 'option_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_option_values');
    }
};

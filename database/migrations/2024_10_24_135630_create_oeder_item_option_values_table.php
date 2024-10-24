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
        Schema::create('oeder_item_option_values', function (Blueprint $table) {
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->cascadeOnDelete();
            $table->foreignId('option_value_id')
                ->constrained('option_values')
                ->cascadeOnDelete();
            $table->unique(['order_item_id', 'option_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oeder_item_option_values');
    }
};

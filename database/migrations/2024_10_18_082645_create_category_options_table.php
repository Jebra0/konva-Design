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
        Schema::create('category_options', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('templates_categories')
                ->cascadeOnDelete();

            $table->foreignId('option_id')
                ->constrained('options')
                ->cascadeOnDelete();
            
            $table->unique(['category_id', 'option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_options');
    }
};

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
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users');
            $table->string('number')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])
                ->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('pending');
            $table->string('payment_method')->nullable();
            $table->float('shipping_cost')->default(0);
            $table->float('tax')->default(0);
            $table->float('discount')->default(0);
            $table->float('total')->default(0);
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

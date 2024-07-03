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
            $table->uuid('id')->primary()->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restorant_id');
            $table->text('details');
            $table->text('orders');
            $table->bigInteger('price');
            $table->enum('type', ['cash', 'ficpay']);
            $table->smallInteger('status');
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

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
        Schema::create('user_fic_pays', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('amount', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_fic_pays');
    }
};

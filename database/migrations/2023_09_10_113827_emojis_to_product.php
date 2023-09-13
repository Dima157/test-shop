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
        Schema::create('emojis_to_product', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('userId');
            $table->integer('productId');
            $table->unsignedBigInteger('emojiId');
            $table->index('userId');
            $table->index('productId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('productId')->references('id')->on('mshop_product')->onDelete('cascade');
            $table->foreign('emojiId')->references('id')->on('emojis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emojis_to_product');
    }
};

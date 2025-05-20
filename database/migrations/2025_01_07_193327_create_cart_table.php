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
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('item_id'); // Add the foreign key column
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade'); // Define the foreign key
            $table->integer('counter');
            $table->unsignedBigInteger('user_id')->nullable(); // Add the foreign key column
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Define the foreign key
            $table->string('guest_token')->nullable();
            $table->decimal('total', 10, 2);
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};

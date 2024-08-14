<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restock_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->integer('ordered_quantity');
            $table->integer('received_quantity')->nullable();
            $table->integer('old_quantity')->nullable();
            $table->decimal('old_buying_price', 10, 2)->nullable();
            $table->decimal('new_buying_price', 10, 2);
            $table->decimal('old_selling_price', 10, 2)->nullable();
            $table->decimal('new_selling_price', 10, 2);
            $table->boolean('price_changed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restock_items');
    }
};

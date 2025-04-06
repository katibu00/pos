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
        Schema::create('awaiting_pickups', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no');
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('sale_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['awaiting', 'delivered'])->default('awaiting');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id'); // User who created the record
            $table->unsignedBigInteger('delivery_user_id')->nullable(); // User who delivered the item
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->foreign('stock_id')->references('id')->on('stocks');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('delivery_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awaiting_pickups');
    }
};

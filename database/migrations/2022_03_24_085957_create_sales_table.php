<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id');
            $table->string('receipt_no');
            $table->integer('stock_id');
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('discount')->default(0);
            $table->string('payment_type');
            $table->integer('payment_amount')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('customer')->nullable();
            $table->string('note')->nullable();
            $table->string('returned_qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}

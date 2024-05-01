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
        Schema::create('cash_credits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->unsignedBigInteger('cashier_id');
            $table->integer('branch_id');
            $table->float('amount');
            $table->float('amount_paid')->default(0); 
            $table->string('status')->nullable(); 
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
        Schema::dropIfExists('cash_credits');
    }
};

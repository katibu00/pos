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
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->enum('from_account', ['cash', 'transfer', 'pos']);
            $table->enum('to_account', ['cash', 'transfer', 'pos']);
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('branch_id'); 
            $table->unsignedBigInteger('cashier_id');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_transfers');
    }
};

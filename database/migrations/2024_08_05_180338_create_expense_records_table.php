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
        Schema::create('expense_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_account_id');
            $table->unsignedBigInteger('cashier_id');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        
            $table->foreign('expense_account_id')->references('id')->on('expense_accounts')->onDelete('cascade');
            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_records');
    }
};

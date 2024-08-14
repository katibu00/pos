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
        Schema::create('restocks', function (Blueprint $table) {
            $table->id();
            $table->string('restock_number')->unique();
            $table->enum('type', ['planned', 'direct']);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->enum('status', ['pending', 'received', 'completed']);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->timestamps();
        
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restocks');
    }
};

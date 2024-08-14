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
        Schema::create('branch_restocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restock_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained();
            $table->decimal('percentage', 5, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_restocks');
    }
};

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
        Schema::create('online_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('online_product_id');
            $table->string('image_url');
            $table->boolean('featured')->default(false);
            $table->timestamps();
        
            $table->foreign('online_product_id')->references('id')->on('online_store_products')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_product_images');
    }
};

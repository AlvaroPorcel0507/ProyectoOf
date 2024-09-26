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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('quantity');
            $table->decimal('unitPrice', 10, 2);
            $table->decimal('totalProduct', 10, 2);
            $table->unsignedBigInteger('salesId');
            $table->unsignedBigInteger('productsId');
            $table->foreign('salesId')->references('id')->on('sales');
            $table->foreign('productsId')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};

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
        Schema::create('unit_products', function (Blueprint $table) {
            $table->id();
            $table->decimal('unitPrice',10,2);
            $table->enum('measurementUnit',['Kilo','Cuartilla','Libra','Arroba','Quintal']);
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('productId');
            $table->foreign('productId')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_products');
    }
};

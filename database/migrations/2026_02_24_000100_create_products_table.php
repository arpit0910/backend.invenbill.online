<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('product_code', 16)->unique();
            $table->string('name');
            $table->string('sku');
            $table->decimal('mrp', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->text('short_description')->nullable();
            $table->longText('detailed_description')->nullable();
            $table->timestamps();

            $table->unique(['created_by', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};


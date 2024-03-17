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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('namaProduk');
            $table->string('barcode')->nullable();
            $table->foreignId('id_kategori')->constrained('categories')->onDelete('cascade');
            $table->foreignId('id_variant')->nullable()->constrained('variants')->onDelete('cascade');
            $table->foreignId('id_toko')->constrained('markets')->onDelete('cascade');
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->string('deskripsi')->nullable();
            $table->integer('stok')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

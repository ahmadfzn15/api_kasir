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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->foreignId('id_toko')->constrained('markets')->onDelete('cascade');
            $table->foreignId('id_kasir')->constrained('users')->onDelete('cascade');
            $table->integer('cash');
            $table->integer('cashback');
            $table->integer('total_harga');
            $table->enum('status', ['Lunas', 'Belum Lunas'])->default('Lunas');
            $table->integer('biaya_tambahan')->nullable();
            $table->string('deskripsi_biaya_tambahan')->nullable();
            $table->integer('diskon')->nullable();
            $table->integer('total_pembayaran');
            $table->string('ket')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

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
        if (!Schema::hasTable('inventori')) {
            Schema::create('inventori', function (Blueprint $table) {
                $table->id();
                $table->string('nama_barang', 200);
                $table->decimal('jumlah_stok', 15, 2)->default(0);
                $table->string('satuan', 20)->default('kg');
                $table->date('tanggal_update')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('inventori_movements')) {
            Schema::create('inventori_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventori_id')->constrained('inventori')->onDelete('cascade');
                $table->string('jenis', 10); // masuk / keluar
                $table->decimal('jumlah', 15, 2);
                $table->string('keterangan', 255)->nullable();
                $table->date('tanggal');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventori_movements');
        Schema::dropIfExists('inventori');
    }
};

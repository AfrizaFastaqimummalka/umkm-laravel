<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('pemasukan')) {
            Schema::create('pemasukan', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->decimal('jumlah',15,0);
                $table->string('keterangan',255)->nullable();
                $table->string('kategori',20)->default('penjualan');
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('pengeluaran')) {
            Schema::create('pengeluaran', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->decimal('jumlah',15,0);
                $table->string('keterangan',255)->nullable();
                $table->string('kategori',20)->default('operasional');
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('pelanggan')) {
            Schema::create('pelanggan', function (Blueprint $table) {
                $table->id();
                $table->string('nama',100);
                $table->string('no_hp',20)->nullable();
                $table->text('alamat')->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('pemasok')) {
            Schema::create('pemasok', function (Blueprint $table) {
                $table->id();
                $table->string('nama',100);
                $table->string('kontak',100)->nullable();
                $table->string('no_hp',20)->nullable();
                $table->text('alamat')->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('inventori')) {
            Schema::create('inventori', function (Blueprint $table) {
                $table->id();
                $table->string('nama_barang',200);
                $table->decimal('jumlah_stok',15,2)->default(0);
                $table->string('satuan',20)->default('kg');
                $table->timestamp('tanggal_update')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('inventori_movements')) {
            Schema::create('inventori_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventori_id')->constrained('inventori')->cascadeOnDelete();
                $table->enum('jenis',['masuk','keluar']);
                $table->decimal('jumlah',15,2);
                $table->string('keterangan',255)->nullable();
                $table->timestamp('tanggal')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }

    }
    public function down(): void
    {
        Schema::dropIfExists('inventori_movements');
        Schema::dropIfExists('inventori');
        Schema::dropIfExists('telegram_subscribers');
        Schema::dropIfExists('pemasok');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('pemasukan');
    }
};

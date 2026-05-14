<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pemasok')) {
            Schema::create('pemasok', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 100);
                $table->string('kontak', 100)->nullable();
                $table->string('no_hp', 20)->nullable();
                $table->text('alamat')->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok');
    }
};

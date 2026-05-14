<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $table    = 'pemasukan';
    protected $fillable = ['tanggal', 'jumlah', 'keterangan', 'kategori', 'user_id'];
    protected $casts    = ['tanggal' => 'date', 'jumlah' => 'decimal:0'];

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'penjualan'  => 'Penjualan Tempe',
            'titip_jual' => 'Titip Jual',
            default      => 'Lainnya',
        };
    }
}

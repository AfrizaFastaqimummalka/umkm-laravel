<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table    = 'pengeluaran';
    protected $fillable = ['tanggal', 'jumlah', 'keterangan', 'kategori', 'user_id'];
    protected $casts    = ['tanggal' => 'date', 'jumlah' => 'decimal:0'];

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'bahan_baku'  => 'Bahan Baku',
            'operasional' => 'Operasional',
            'gaji'        => 'Gaji Karyawan',
            'listrik_air' => 'Listrik & Air',
            default       => 'Lainnya',
        };
    }
}

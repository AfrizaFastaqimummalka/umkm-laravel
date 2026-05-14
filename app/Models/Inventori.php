<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventori extends Model
{
    protected $table    = 'inventori';
    protected $fillable = ['nama_barang','jumlah_stok','satuan','tanggal_update','user_id'];

    public function movements(): HasMany
    {
        return $this->hasMany(InventoriMovement::class,'inventori_id');
    }

    public function getSatuanLabelAttribute(): string
    {
        return match($this->satuan) {
            'kg'    => 'Kilogram',
            'ons'   => 'Ons',
            'pcs'   => 'Pieces',
            'pack'  => 'Pack',
            'zak'   => 'Zak',
            'liter' => 'Liter',
            default => ucfirst($this->satuan),
        };
    }
}

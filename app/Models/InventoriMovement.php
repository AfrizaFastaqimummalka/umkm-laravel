<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoriMovement extends Model
{
    protected $table    = 'inventori_movements';
    protected $fillable = ['inventori_id','jenis','jumlah','keterangan','tanggal','user_id'];
}

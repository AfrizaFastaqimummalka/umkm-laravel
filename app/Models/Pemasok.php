<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    protected $table    = 'pemasok';
    protected $fillable = ['nama','kontak','no_hp','alamat','catatan','user_id'];
}

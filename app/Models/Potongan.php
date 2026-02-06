<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    use HasFactory;

    protected $table = 'potongan';
    protected $primaryKey = 'id_potongan';
    protected $fillable = ['nama_potongan', 'nominal'];
    public $timestamps = true;

    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_potongan', 'id_potongan', 'id_pegawai', 'id_potongan', 'id_pegawai');
    }
}

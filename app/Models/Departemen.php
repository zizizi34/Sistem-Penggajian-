<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';
    protected $primaryKey = 'id_departemen';
    protected $fillable = ['nama_departemen', 'manager_departemen'];
    public $timestamps = true;

    public function jabatans()
    {
        return $this->hasMany(Jabatan::class, 'id_departemen', 'id_departemen');
    }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'id_departemen', 'id_departemen');
    }

    public function jadwalKerja()
    {
        return $this->hasOne(JadwalKerja::class, 'id_departemen', 'id_departemen');
    }
}

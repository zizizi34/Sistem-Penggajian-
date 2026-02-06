<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    use HasFactory;

    protected $table = 'tunjangan';
    protected $primaryKey = 'id_tunjangan';
    protected $fillable = ['nama_tunjangan', 'nominal'];
    public $timestamps = true;

    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_tunjangan', 'id_tunjangan', 'id_pegawai', 'id_tunjangan', 'id_pegawai');
    }
}

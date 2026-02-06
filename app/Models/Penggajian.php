<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajian';
    protected $primaryKey = 'id_penggajian';
    protected $fillable = [
        'id_pegawai', 'periode', 'gaji_pokok', 'total_tunjangan',
        'total_potongan', 'lembur', 'pajak_pph21', 'gaji_bersih',
        'tanggal_transfer', 'status'
    ];
    public $timestamps = true;

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}

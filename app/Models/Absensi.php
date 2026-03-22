<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'id_pegawai', 'tanggal_absensi', 'jam_masuk', 'jam_pulang', 
        'status', 'keterangan', 'foto_masuk', 'foto_pulang',
        'approved_at', 'approved_by',
        'correction_requested', 'correction_type', 'correction_value', 'correction_reason', 'correction_requested_at'
    ];
    public $timestamps = true;

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}

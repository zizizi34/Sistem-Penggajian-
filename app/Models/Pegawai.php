<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    protected $fillable = [
        'nik_pegawai', 'nama_pegawai', 'jenis_kelamin', 'tanggal_lahir',
        'alamat', 'no_hp', 'email_pegawai', 'bank_pegawai', 'no_rekening',
        'npwp', 'id_ptkp_status', 'id_jabatan', 'status_pegawai',
        'tgl_masuk', 'gaji_pokok', 'id_departemen'
    ];
    public $timestamps = true;

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function ptkpStatus()
    {
        return $this->belongsTo(PtkpStatus::class, 'id_ptkp_status', 'id_ptkp_status');
    }

    public function tunjangans()
    {
        return $this->belongsToMany(Tunjangan::class, 'pegawai_tunjangan', 'id_pegawai', 'id_tunjangan', 'id_pegawai', 'id_tunjangan');
    }

    public function potongans()
    {
        return $this->belongsToMany(Potongan::class, 'pegawai_potongan', 'id_pegawai', 'id_potongan', 'id_pegawai', 'id_potongan');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_pegawai', 'id_pegawai');
    }

    public function lemburs()
    {
        return $this->hasMany(Lembur::class, 'id_pegawai', 'id_pegawai');
    }

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'id_pegawai', 'id_pegawai');
    }
}

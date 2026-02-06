<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permission';
    protected $primaryKey = 'id_permission';
    protected $fillable = ['nama_permission', 'deskripsi', 'kategori'];
    public $timestamps = true;

    /**
     * Relasi dengan role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission', 'id_permission', 'id_role');
    }
}

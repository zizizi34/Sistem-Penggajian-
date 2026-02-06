<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'id_role';
    protected $fillable = ['nama_role', 'deskripsi_role'];
    public $timestamps = true;

    /**
     * Relasi dengan permission
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'id_role', 'id_permission');
    }

    /**
     * Cek apakah role memiliki permission tertentu
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('nama_permission', $permissionName)->exists();
    }

    /**
     * Cek apakah role memiliki salah satu dari beberapa permission
     */
    public function hasAnyPermission($permissions)
    {
        return $this->permissions()->whereIn('nama_permission', $permissions)->exists();
    }

    /**
     * Cek apakah role memiliki semua permission
     */
    public function hasAllPermissions($permissions)
    {
        $count = $this->permissions()->whereIn('nama_permission', $permissions)->count();
        return $count === count($permissions);
    }
}

<?php

namespace App\Models;

/**
 * Trait untuk checking permission pada User
 */
trait HasPermissions
{
    /**
     * Relasi dengan Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Cek apakah user memiliki permission tertentu
     * 
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionName);
    }

    /**
     * Cek apakah user memiliki salah satu dari beberapa permission
     * 
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasAnyPermission($permissions);
    }

    /**
     * Cek apakah user memiliki semua permission
     * 
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions($permissions)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasAllPermissions($permissions);
    }

    /**
     * Cek apakah user memiliki role yang spesifik
     * 
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->nama_role === $roleName;
    }

    /**
     * Cek apakah user memiliki salah satu role dari beberapa role
     * 
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyRole($roleNames)
    {
        if (!$this->role) {
            return false;
        }

        return in_array($this->role->nama_role, $roleNames);
    }

    /**
     * Cek apakah user adalah Super Admin / Admin
     * 
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasAnyRole(['Admin HRD', 'Administrator', 'Super Admin']);
    }

    /**
     * Cek apakah user adalah Admin
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isSuperAdmin();
    }
}

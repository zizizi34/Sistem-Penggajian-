<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionName);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasAllPermissions($permissions);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->nama_role === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (!$this->role) {
            return false;
        }

        return in_array($this->role->nama_role, $roles);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is admin (Admin HRD or Super Admin)
     */
    public function isAdmin()
    {
        return $this->hasAnyRole(['Admin HRD', 'Super Admin']);
    }
}

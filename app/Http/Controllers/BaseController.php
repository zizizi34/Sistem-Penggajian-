<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Http\Request;

/**
 * BaseController - Parent Controller untuk Production Ready System
 * 
 * Controller ini menyediakan base methods untuk:
 * - Permission checking
 * - Department scoping untuk Officer
 * - Response formatting
 * - Activity logging
 * 
 * Semua controller aplikasi harus extend controller ini.
 * 
 * @author Your Name
 * @version 1.0
 */
class BaseController extends LaravelController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Current authenticated user
     */
    protected $user;

    /**
     * Current user role
     */
    protected $userRole;

    /**
     * Current user department (for Officer)
     */
    protected $userDepartment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            // Load current user ke property
            $this->user = auth()->user() ?? auth('officer')->user() ?? auth('student')->user();
            
            if ($this->user) {
                // Detect user role
                if (auth('officer')->check()) {
                    $this->userRole = 'Petugas';
                    $this->userDepartment = auth('officer')->user()->id_departemen;
                } elseif (auth('student')->check()) {
                    $this->userRole = 'Pegawai';
                } elseif ($this->user->role) {
                    $this->userRole = $this->user->role->nama_role;
                }
            }

            return $next($request);
        });
    }

    /**
     * Cek apakah user punya permission tertentu
     * 
     * @param  string $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {
        if (!$this->user) {
            return false;
        }

        // Super Admin always return true
        if ($this->userRole === 'Super Admin') {
            return true;
        }

        // Untuk user dengan role (via User model)
        if ($this->user->role && method_exists($this->user, 'hasPermission')) {
            return $this->user->hasPermission($permission);
        }

        return false;
    }

    /**
     * Cek apakah user punya salah satu dari beberapa permission
     * 
     * @param  array $permissions
     * @return bool
     */
    protected function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah user punya semua permission
     * 
     * @param  array $permissions
     * @return bool
     */
    protected function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Abort jika user tidak punya permission
     * 
     * @param  string $permission
     * @param  string $message
     * @return void
     */
    protected function authorize($permission, $message = 'Unauthorized')
    {
        if (!$this->hasPermission($permission)) {
            abort(403, $message);
        }
    }

    /**
     * Get user department ID (untuk Officer/Petugas)
     * 
     * @return int|null
     */
    protected function getUserDepartmentId()
    {
        return $this->userDepartment;
    }

    /**
     * Get user department scope untuk query builder
     * Untuk Officer: hanya departemen sendiri
     * Untuk Super Admin: null (tidak ada filter)
     * 
     * @return int|null
     */
    protected function getDepartmentScope()
    {
        if ($this->userRole === 'Petugas') {
            return $this->userDepartment;
        }
        return null;
    }

    /**
     * Get user pegawai scope untuk query builder
     * Untuk Pegawai: hanya data pribadi
     * Untuk yang lain: null
     * 
     * @return int|null
     */
    protected function getPegawaiScope()
    {
        if ($this->userRole === 'Pegawai' && $this->user && $this->user->id_pegawai) {
            return $this->user->id_pegawai;
        }
        return null;
    }

    /**
     * Check user role
     * 
     * @param  string $role
     * @return bool
     */
    protected function isRole($role)
    {
        return $this->userRole === $role;
    }

    /**
     * Check user is Super Admin
     * 
     * @return bool
     */
    protected function isSuperAdmin()
    {
        return $this->isRole('Super Admin');
    }

    /**
     * Check user is Petugas (Officer)
     * 
     * @return bool
     */
    protected function isOfficer()
    {
        return $this->isRole('Petugas');
    }

    /**
     * Check user is Pegawai (Employee)
     * 
     * @return bool
     */
    protected function isPegawai()
    {
        return $this->isRole('Pegawai');
    }

    /**
     * Response JSON Success
     * 
     * @param  mixed $data
     * @param  string $message
     * @param  int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseSuccess($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Response JSON Error
     * 
     * @param  string $message
     * @param  int $status
     * @param  mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseError($message = 'Error', $status = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Response JSON Unauthorized
     * 
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseUnauthorized($message = 'Unauthorized')
    {
        return $this->responseError($message, 401);
    }

    /**
     * Response JSON Forbidden
     * 
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseForbidden($message = 'Forbidden')
    {
        return $this->responseError($message, 403);
    }

    /**
     * Response JSON Not Found
     * 
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseNotFound($message = 'Not Found')
    {
        return $this->responseError($message, 404);
    }

    /**
     * Activity Logging - Log user action untuk audit trail
     * 
     * @param  string $action
     * @param  string $model
     * @param  mixed $modelId
     * @param  string $description
     * @param  mixed $oldValues
     * @param  mixed $newValues
     * @return void
     */
    protected function logActivity($action, $model, $modelId = null, $description = null, $oldValues = null, $newValues = null)
    {
        if (!$this->user) {
            return;
        }

        try {
            \DB::table('activity_logs')->insert([
                'user_id' => $this->user->id_user ?? $this->user->id,
                'user_type' => $this->userRole,
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'description' => $description,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Jika table tidak exist, skip logging
            // Tidak perlu error jika logging gagal
        }
    }
}

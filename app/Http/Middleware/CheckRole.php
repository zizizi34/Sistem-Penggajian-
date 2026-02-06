<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware untuk mengecek role user
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Super Admin dapat akses semua role-based resource
        try {
            if ($user->role && in_array($user->role->nama_role, ['Admin HRD', 'Administrator', 'Super Admin'])) {
                return $next($request);
            }
            
            // Check requested roles
            $roles = array_slice(func_get_args(), 2);
            foreach ($roles as $roleCheck) {
                if ($user->hasRole($roleCheck)) {
                    return $next($request);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking role: ' . $e->getMessage()
            ], 403);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Forbidden: Role Anda tidak memiliki akses ke resource ini'
        ], 403);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware untuk mengecek permission user
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Super Admin / Administrator memiliki akses penuh ke semua resource
        // Check by role name - untuk admin
        try {
            if ($user->role && in_array($user->role->nama_role, ['Admin HRD', 'Administrator', 'Super Admin'])) {
                return $next($request);
            }
            
            // Jika role ada, check permission
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // Jika ada error di role checking, deny akses dengan pesan error
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking permission: ' . $e->getMessage()
            ], 403);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Forbidden: Anda tidak memiliki akses ke resource ini'
        ], 403);
    }
}

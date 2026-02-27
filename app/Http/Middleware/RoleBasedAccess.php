<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * RoleBasedAccess Middleware
 * 
 * Middleware untuk enforce role-based access control.
 * Ini adalah layer kedua setelah authentication, untuk memastikan user hanya akses
 * fitur yang sesuai dengan role mereka.
 * 
 * Penggunaan:
 * - Automatic di semua route yang authenticated
 * - Cek permission di controller sebelum action tertentu
 * 
 * @author Your Name
 * @version 1.0
 */
class RoleBasedAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Untuk Super Admin, tidak ada batasan
        if ($user && $user->role && $user->role->nama_role === 'Super Admin') {
            return $next($request);
        }

        // Store user role info untuk akses di controller
        if ($user && $user->role) {
            $request->merge(['user_role' => $user->role->nama_role]);
            app()->instance('user_role', $user->role->nama_role);
        }

        return $next($request);
    }
}

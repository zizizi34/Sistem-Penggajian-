<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware untuk administrator routes
 * Super Admin / Administrator memiliki akses penuh ke semua resource
 */
class AdministratorAccess
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
        // User harus authenticated sebagai administrator
        if (!$request->user('administrator') && !$request->user('web')) {
            return redirect()->route('login');
        }

        // Super Admin / Administrator memiliki akses penuh
        // No permission check needed for admin
        return $next($request);
    }
}

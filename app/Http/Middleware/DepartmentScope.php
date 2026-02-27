<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * DepartmentScope Middleware
 * 
 * Middleware ini memastikan Officer (Petugas) hanya dapat mengakses data dari departemen mereka.
 * Middleware ini berjalan di background dan otomatis memfilter query.
 * 
 * Penggunaan:
 * - Route::middleware('department.scope')->group(...)
 * 
 * @author Your Name
 * @version 1.0
 */
class DepartmentScope
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

        // Jika user adalah Officer (authenticated via officer guard)
        if (auth('officer')->check()) {
            $officer = auth('officer')->user();
            
            // Store officer department in request untuk akses di controller
            $request->merge(['officer_department_id' => $officer->id_departemen]);

            // Store di dalam service container untuk global access
            app()->instance('officer_department_id', $officer->id_departemen);
        }

        return $next($request);
    }
}

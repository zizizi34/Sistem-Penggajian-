<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HrOnlyAccess
 *
 * Middleware untuk membatasi akses fitur Penggajian hanya kepada
 * officer yang berasal dari departemen Human Resources.
 *
 * Dalam struktur HRIS yang benar, hanya tim HR/Payroll yang berhak
 * menghitung dan mengelola gaji seluruh pegawai perusahaan.
 */
class HrOnlyAccess
{
    // Nama departemen HR yang diizinkan (case-insensitive akan dibandingkan lowercase)
    private const HR_DEPARTMENT_NAMES = [
        'human resources',
        'hr',
        'hrd',
        'human resource department',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $officer = auth('officer')->user();

        if (!$officer) {
            return redirect()->route('login');
        }

        // Cek apakah officer berasal dari departemen Human Resources
        $departemen = $officer->departemen;

        if (!$departemen) {
            return redirect()->route('officers.dashboard')
                ->with('error', 'Anda tidak memiliki departemen. Hubungi Super Admin.');
        }

        $namaDept = strtolower(trim($departemen->nama_departemen));
        $isHr = false;

        foreach (self::HR_DEPARTMENT_NAMES as $hrName) {
            if (str_contains($namaDept, $hrName)) {
                $isHr = true;
                break;
            }
        }

        if (!$isHr) {
            return redirect()->route('officers.dashboard')
                ->with('error', 'Akses ditolak. Fitur Penggajian hanya dapat diakses oleh tim Human Resources.');
        }

        return $next($request);
    }
}

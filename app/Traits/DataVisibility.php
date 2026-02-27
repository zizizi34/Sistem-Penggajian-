<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * DataVisibility Trait
 * 
 * Trait ini menyediakan automatic scoping untuk model query berdasarkan user role.
 * Ketika query dijalankan, data akan otomatis difilter sesuai permission user.
 * 
 * Penggunaan di Model:
 * use App\Traits\DataVisibility;
 * 
 * class YourModel extends Model {
 *     use DataVisibility;
 * }
 * 
 * Kemudian di query:
 * YourModel::whereVisible()->get();
 * // atau
 * YourModel::visible()->get();
 * 
 * @author Your Name
 * @version 1.0
 */
trait DataVisibility
{
    /**
     * Boot trait
     */
    public static function bootDataVisibility()
    {
        // Auto apply visibility scope ketika query
        // static::addGlobalScope('visibility', function (Builder $builder) {
        //     $builder->whereVisible();
        // });
    }

    /**
     * Scope untuk lihat data sesuai role user
     */
    public function scopeVisible(Builder $query)
    {
        return $this->applyScopeBasedOnRole($query);
    }

    /**
     * Apply scope berdasarkan role user
     */
    private function applyScopeBasedOnRole(Builder $query)
    {
        $user = auth()->user();
        
        // Jika tidak ada user, return query as is
        if (!$user) {
            return $query;
        }

        // Super Admin bisa lihat semua
        if ($user->role && $user->role->nama_role === 'Super Admin') {
            return $query;
        }

        // Officer hanya lihat departemen sendiri
        if (auth('officer')->check()) {
            $officer = auth('officer')->user();
            
            // Filter berdasarkan department field (adjust sesuai model)
            if (isset($this->fillable) && in_array('id_departemen', $this->fillable)) {
                return $query->where('id_departemen', $officer->id_departemen);
            }
            
            // Untuk relation, filter via pegawai
            if (method_exists($this, 'pegawai')) {
                return $query->whereHas('pegawai', function ($q) use ($officer) {
                    $q->where('id_departemen', $officer->id_departemen);
                });
            }
        }

        // Pegawai hanya lihat data pribadi mereka
        if (auth('student')->check() || $user->role->nama_role === 'Pegawai') {
            // Get pegawai ID dari user
            if ($user->id_pegawai) {
                return $query->where('id_pegawai', $user->id_pegawai);
            }
        }

        return $query;
    }

    /**
     * Check apakah model ini punya id_pegawai column
     */
    public function hasPegawaiScope()
    {
        return in_array('id_pegawai', $this->fillable ?? []);
    }

    /**
     * Check apakah model ini punya id_departemen column
     */
    public function hasDepartemenScope()
    {
        return in_array('id_departemen', $this->fillable ?? []);
    }
}

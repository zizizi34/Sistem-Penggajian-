<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // MySQL ENUM update
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','izin','alpha','approved','Lupa Absen Pulang','Lembur tetapi Lupa Absen Pulang','Lembur') DEFAULT 'hadir'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','izin','alpha','approved') DEFAULT 'hadir'");
        });
    }
};

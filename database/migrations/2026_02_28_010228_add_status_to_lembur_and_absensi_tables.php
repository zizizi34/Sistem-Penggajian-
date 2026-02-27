<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('keterangan');
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
        });

        // absensi already has an ENUM('hadir','izin','alpha'). MySQL allows altering ENUM by redeclaring it.
        DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','izin','alpha','approved') DEFAULT 'hadir'");
        
        Schema::table('absensi', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembur_and_absensi_tables', function (Blueprint $table) {
            //
        });
    }
};

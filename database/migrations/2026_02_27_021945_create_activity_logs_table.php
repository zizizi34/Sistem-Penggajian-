<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Activity Logs Table
 * 
 * Table untuk menyimpan audit trail dari semua aktivitas user.
 * Berguna untuk:
 * - Tracking siapa yang mengubah data apa
 * - Compliance & audit requirements
 * - Troubleshooting & debugging
 * - Security monitoring
 * 
 * @author Your Name
 * @version 1.0
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    
    // User information
    $table->unsignedBigInteger('user_id')->index();
    $table->string('user_type', 50)->comment('Super Admin|Petugas|Pegawai');
    $table->string('user_email', 150)->nullable();
    $table->string('user_name', 100)->nullable();
    
    // Activity information
    $table->string('action', 50)->comment('create|read|update|delete|approve');
    $table->string('model', 100)->comment('Absensi|Lembur|Penggajian|etc');
    $table->unsignedBigInteger('model_id')->nullable();
    $table->text('description')->nullable();
    
    // Change tracking
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    
    // Request information
    $table->string('ip_address', 45)->nullable(); // support IPv6
    $table->text('user_agent')->nullable();
    $table->string('method', 10)->nullable();
    $table->string('url', 255)->nullable();
    $table->integer('response_code')->nullable();
    
    // Timestamps
    $table->timestamps();
    
    // Indexes (SUDAH AMAN)
    $table->index(['user_id', 'created_at']);
    $table->index(['action', 'model']);
    $table->index(['model', 'model_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

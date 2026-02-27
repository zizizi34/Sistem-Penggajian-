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
            $table->string('user_type')->comment('Super Admin|Petugas|Pegawai');
            $table->string('user_email')->nullable();
            $table->string('user_name')->nullable();
            
            // Activity information
            $table->string('action')->comment('create|read|update|delete|approve');
            $table->string('model')->comment('Absensi|Lembur|Penggajian|etc');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            
            // Change tracking
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Request information
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable()->comment('GET|POST|PUT|DELETE');
            $table->string('url')->nullable();
            $table->integer('response_code')->nullable();
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Indexes untuk quick filtering
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

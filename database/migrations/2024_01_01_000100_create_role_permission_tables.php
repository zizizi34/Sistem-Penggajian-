<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel Role
        if (!Schema::hasTable('role')) {
            Schema::create('role', function (Blueprint $table) {
                $table->id('id_role');
                $table->string('nama_role')->unique();
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        // Tabel Permission
        if (!Schema::hasTable('permission')) {
            Schema::create('permission', function (Blueprint $table) {
                $table->id('id_permission');
                $table->string('nama_permission')->unique();
                $table->text('deskripsi')->nullable();
                $table->string('kategori')->nullable();
                $table->timestamps();
            });
        }

        // Tabel Junction: Role_Permission
        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_role');
                $table->unsignedBigInteger('id_permission');
                $table->timestamps();

                $table->foreign('id_role')
                    ->references('id_role')
                    ->on('role')
                    ->onDelete('cascade');

                $table->foreign('id_permission')
                    ->references('id_permission')
                    ->on('permission')
                    ->onDelete('cascade');

                $table->unique(['id_role', 'id_permission']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permission');
        Schema::dropIfExists('role');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollSchema extends Migration
{
    public function up()
    {
        // Skip role and role_permission - they're already created by 2024_01_01_000100_create_role_permission_tables

        Schema::create('departemen', function (Blueprint $table) {
            $table->bigIncrements('id_departemen');
            $table->string('nama_departemen');
            $table->unsignedBigInteger('manager_departemen')->nullable();
            $table->timestamps();
        });

        Schema::create('ptkp_status', function (Blueprint $table) {
            $table->bigIncrements('id_ptkp_status');
            $table->string('kode_ptkp_status')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->bigIncrements('id_jabatan');
            $table->string('nama_jabatan');
            $table->decimal('min_gaji',15,2)->nullable();
            $table->decimal('max_gaji',15,2)->nullable();
            $table->unsignedBigInteger('id_departemen')->nullable();
            $table->timestamps();

            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')->onDelete('set null');
        });

        Schema::create('pegawai', function (Blueprint $table) {
            $table->bigIncrements('id_pegawai');
            $table->string('nik_pegawai')->nullable();
            $table->string('nama_pegawai');
            $table->enum('jenis_kelamin',['L','P'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email_pegawai')->nullable();
            $table->string('bank_pegawai')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('npwp')->nullable();
            $table->unsignedBigInteger('id_ptkp_status')->nullable();
            $table->unsignedBigInteger('id_jabatan')->nullable();
            $table->enum('status_pegawai',['aktif','nonaktif'])->default('aktif');
            $table->date('tgl_masuk')->nullable();
            $table->decimal('gaji_pokok',15,2)->default(0);
            $table->unsignedBigInteger('id_departemen')->nullable();
            $table->timestamps();

            $table->foreign('id_ptkp_status')->references('id_ptkp_status')->on('ptkp_status')->onDelete('set null');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatan')->onDelete('set null');
            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')->onDelete('set null');
        });

        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('id_user');
            $table->string('email_user')->unique();
            $table->string('password_user');
            $table->unsignedBigInteger('id_role')->nullable();
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->timestamps();

            $table->foreign('id_role')->references('id_role')->on('role')->onDelete('set null');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('set null');
        });

        Schema::create('potongan', function (Blueprint $table) {
            $table->bigIncrements('id_potongan');
            $table->string('nama_potongan');
            $table->decimal('nominal',15,2)->default(0);
            $table->timestamps();
        });

        Schema::create('pegawai_potongan', function (Blueprint $table) {
            $table->bigIncrements('id_pegawai_potongan');
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_potongan');
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
            $table->foreign('id_potongan')->references('id_potongan')->on('potongan')->onDelete('cascade');
        });

        Schema::create('tunjangan', function (Blueprint $table) {
            $table->bigIncrements('id_tunjangan');
            $table->string('nama_tunjangan');
            $table->decimal('nominal',15,2)->default(0);
            $table->timestamps();
        });

        Schema::create('pegawai_tunjangan', function (Blueprint $table) {
            $table->bigIncrements('id_pegawai_tunjangan');
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_tunjangan');
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
            $table->foreign('id_tunjangan')->references('id_tunjangan')->on('tunjangan')->onDelete('cascade');
        });

        Schema::create('absensi', function (Blueprint $table) {
            $table->bigIncrements('id_absensi');
            $table->unsignedBigInteger('id_pegawai');
            $table->date('tanggal_absensi');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status',['hadir','izin','alpha'])->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
        });

        Schema::create('lembur', function (Blueprint $table) {
            $table->bigIncrements('id_lembur');
            $table->unsignedBigInteger('id_pegawai');
            $table->date('tanggal_lembur');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->decimal('durasi',8,2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
        });

        Schema::create('penggajian', function (Blueprint $table) {
            $table->bigIncrements('id_penggajian');
            $table->unsignedBigInteger('id_pegawai');
            $table->string('periode')->nullable();
            $table->decimal('gaji_pokok',15,2)->default(0);
            $table->decimal('total_tunjangan',15,2)->default(0);
            $table->decimal('total_potongan',15,2)->default(0);
            $table->decimal('lembur',15,2)->default(0);
            $table->decimal('pajak_pph21',15,2)->default(0);
            $table->decimal('gaji_bersih',15,2)->default(0);
            $table->date('tanggal_transfer')->nullable();
            $table->enum('status',['pending','paid','canceled'])->default('pending');
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penggajian');
        Schema::dropIfExists('lembur');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('pegawai_tunjangan');
        Schema::dropIfExists('tunjangan');
        Schema::dropIfExists('pegawai_potongan');
        Schema::dropIfExists('potongan');
        Schema::dropIfExists('user');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('ptkp_status');
        Schema::dropIfExists('departemen');
        // Don't drop role and role_permission - they're managed by 2024_01_01_000100_create_role_permission_tables
    }
}

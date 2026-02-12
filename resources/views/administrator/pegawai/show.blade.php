@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('description', 'Detail Data Pegawai')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Detail Pegawai</h4>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">NIK</label>
            <p>{{ $pegawai->nik_pegawai ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nama</label>
            <p>{{ $pegawai->nama_pegawai ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <p>{{ $pegawai->email_pegawai ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">No HP</label>
            <p>{{ $pegawai->no_hp ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Jenis Kelamin</label>
            <p>{{ $pegawai->jenis_kelamin ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal Lahir</label>
            <p>{{ $pegawai->tanggal_lahir ? date('d M Y', strtotime($pegawai->tanggal_lahir)) : '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Departemen</label>
            <p>{{ $pegawai->departemen->nama_departemen ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jabatan</label>
            <p>{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Gaji Pokok</label>
            <p>Rp {{ number_format($pegawai->gaji_pokok ?? 0, 0, ',', '.') }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <p>
              <span class="badge bg-{{ $pegawai->status_pegawai == 'aktif' ? 'success' : 'danger' }}">
                {{ ucfirst($pegawai->status_pegawai ?? 'aktif') }}
              </span>
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">NPWP</label>
            <p>{{ $pegawai->npwp ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status PTKP</label>
            <p>{{ $pegawai->ptkpStatus->deskripsi ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Bank</label>
            <p>{{ $pegawai->bank_pegawai ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">No Rekening</label>
            <p>{{ $pegawai->no_rekening ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label">Alamat</label>
            <p>{{ $pegawai->alamat ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Tanggal Masuk</label>
            <p>{{ $pegawai->tgl_masuk ? date('d M Y', strtotime($pegawai->tgl_masuk)) : '-' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Aksi</h4>
      </div>
      <div class="card-body">
        <a href="{{ route('administrators.pegawai.index') }}" class="btn btn-secondary w-100">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

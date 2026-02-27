@extends('layouts.app')

@section('title', 'Detail Penggajian')
@section('description', 'Detail Data Penggajian')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Detail Penggajian</h4>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Nama Pegawai</label>
            <p>{{ $penggajian->pegawai->nama_pegawai ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Periode</label>
            <p>{{ $penggajian->periode ?? '-' }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Gaji Pokok</label>
            <p>Rp {{ number_format($penggajian->gaji_pokok ?? 0, 0, ',', '.') }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Lembur</label>
            <p>Rp {{ number_format($penggajian->lembur ?? 0, 0, ',', '.') }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Total Tunjangan</label>
            <p>Rp {{ number_format($penggajian->total_tunjangan ?? 0, 0, ',', '.') }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Total Potongan</label>
            <p>Rp {{ number_format($penggajian->total_potongan ?? 0, 0, ',', '.') }}</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">PPh 21</label>
            <p>Rp {{ number_format($penggajian->pajak_pph21 ?? 0, 0, ',', '.') }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Gaji Bersih</label>
            <p><strong>Rp {{ number_format($penggajian->gaji_bersih ?? 0, 0, ',', '.') }}</strong></p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Tanggal Transfer</label>
            <p>{{ $penggajian->tanggal_transfer ? date('d M Y', strtotime($penggajian->tanggal_transfer)) : '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <p>
              <span class="badge bg-{{ $penggajian->status == 'pending' ? 'warning' : 'success' }}">
                {{ ucfirst($penggajian->status ?? 'pending') }}
              </span>
            </p>
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
        <a href="{{ route('officers.penggajian.index') }}" class="btn btn-secondary w-100 mb-2">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

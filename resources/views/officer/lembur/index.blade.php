@extends('layouts.app')

@section('title', 'Data Lembur Tim Saya')
@section('description', 'Kelola Data Lembur')

@section('content')

@include('utilities.alert')

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Daftar Lembur Tim Saya</h4>
        <span class="badge bg-primary">{{ $lembur->count() }} Data</span>
      </div>
      <div class="card-body">
        @if($lembur->count() > 0)
          {{-- Notifikasi jika ada lembur yang sedang berjalan --}}
          @php
            $sedangLembur = $lembur->filter(function($item) {
              return $item->status === 'pending' && $item->tanggal_lembur === now()->format('Y-m-d') && is_null($item->jam_selesai) === false;
            });
          @endphp

          @if($sedangLembur->count() > 0)
          <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
              <strong>{{ $sedangLembur->count() }} pegawai</strong> sedang dalam status lembur hari ini dan belum absen pulang.
              Data lembur di bawah diperbarui otomatis saat halaman dibuka.
            </div>
          </div>
          @endif

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Pegawai</th>
                  <th>Tanggal</th>
                  <th>Mulai Lembur</th>
                  <th>Selesai</th>
                  <th>Durasi (Jam)</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($lembur as $item)
                <tr>
                  <td class="fw-semibold">{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                  <td>{{ \Carbon\Carbon::parse($item->tanggal_lembur)->format('d/m/Y') }}</td>
                  <td>
                    <span class="badge bg-secondary">
                      <i class="bi bi-clock me-1"></i>{{ $item->jam_mulai ?? '-' }}
                    </span>
                  </td>
                  <td>
                    @if($item->jam_selesai)
                      <span class="badge bg-dark">{{ $item->jam_selesai }}</span>
                    @else
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-hourglass-split me-1"></i>Sedang Berjalan
                      </span>
                    @endif
                  </td>
                  <td>
                    @if($item->durasi)
                      <span class="badge bg-info text-dark">{{ number_format($item->durasi, 2) }} jam</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <small class="text-muted">{{ Str::limit($item->keterangan, 60) ?? '-' }}</small>
                  </td>
                  <td>
                    @if($item->status === 'approved')
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Telah Disetujui
                      </span>
                    @elseif($item->status === 'pending')
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock-history me-1"></i>Menunggu Persetujuan
                      </span>
                    @else
                      <span class="badge bg-secondary">{{ $item->status ?? '-' }}</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="alert alert-light border mt-2 small text-muted">
            <i class="bi bi-info-circle me-1 text-primary"></i>
            Data lembur dengan status <strong>"Sedang Berjalan"</strong> adalah lembur yang terdeteksi otomatis
            dari pegawai yang belum absen pulang melewati jam jadwal. Data akan diperbarui setiap kali halaman dimuat.
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="bi bi-clock-history fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-1 fw-semibold">Belum ada data lembur untuk tim Anda.</p>
            <small>Data lembur akan muncul otomatis jika ada pegawai yang bekerja melewati jam jadwal pulang.</small>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

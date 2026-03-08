@extends('layouts.app')

@section('title', 'Data Penggajian')
@section('description', 'Kelola Data Penggajian Bulanan Departemen')

@section('content')
<div class="row mb-3">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h4 class="card-title mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>Penggajian Bulanan</h4>
          <small class="text-muted">HR Officer — Kelola penggajian departemen Anda</small>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
          {{-- Filter Periode --}}
          <form method="GET" action="{{ route('officers.penggajian.index') }}" class="d-flex gap-2 align-items-center">
            <input type="month" name="periode" id="filterPeriode" class="form-control form-control-sm"
              value="{{ $periodeFilter }}"
              onchange="this.form.submit()">
            @if($periodeFilter)
              <a href="{{ route('officers.penggajian.index') }}" class="btn btn-outline-secondary btn-sm text-nowrap">
                <i class="bi bi-x-circle"></i> Reset
              </a>
            @endif
          </form>

          {{-- Tombol Hitung Gaji --}}
          <button type="button" class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalHitungGaji">
            <i class="bi bi-calculator"></i> Hitung Gaji
          </button>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card-body">

        @if($periodeFilter && $periodeLabel)
          <div class="alert alert-info border-0 py-2 mb-3">
            <i class="bi bi-calendar-month me-2"></i>
            Menampilkan data penggajian bulan: <strong>{{ $periodeLabel }}</strong>
          </div>
        @endif

        @if($penggajian->count() > 0)

          @php
            $totalBersih  = $penggajian->sum('gaji_bersih');
            $totalPending = $penggajian->where('status','pending')->count();
          @endphp
          <div class="row mb-3 g-3">
            <div class="col-md-4">
              <div class="bg-success bg-opacity-10 rounded p-3 text-center border border-success border-opacity-25">
                <div class="fw-bold text-success fs-5">Rp {{ number_format($totalBersih, 0, ',', '.') }}</div>
                <small class="text-muted">Total Gaji Bersih</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-warning bg-opacity-10 rounded p-3 text-center border border-warning border-opacity-25">
                <div class="fw-bold text-warning fs-5">{{ $totalPending }}</div>
                <small class="text-muted">Pending Pembayaran</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-primary bg-opacity-10 rounded p-3 text-center border border-primary border-opacity-25">
                <div class="fw-bold text-primary fs-5">{{ $penggajian->pluck('id_pegawai')->unique()->count() }}</div>
                <small class="text-muted">Jumlah Pegawai</small>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Nama Pegawai</th>
                  <th>Periode</th>
                  <th>Gaji Pokok</th>
                  <th>Tunjangan</th>
                  <th>Lembur</th>
                  <th>Potongan</th>
                  <th>PPh 21</th>
                  <th class="text-success">Gaji Bersih</th>
                  <th>Tgl Transfer</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($penggajian as $item)
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $item->pegawai->nama_pegawai ?? '-' }}</div>
                    <small class="text-muted">{{ $item->pegawai->jabatan->nama_jabatan ?? '' }}</small>
                  </td>
                  <td><span class="badge bg-secondary">{{ $item->periode ?? '-' }}</span></td>
                  <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                  <td class="text-success">+ Rp {{ number_format($item->total_tunjangan ?? 0, 0, ',', '.') }}</td>
                  <td class="text-info">+ Rp {{ number_format($item->lembur ?? 0, 0, ',', '.') }}</td>
                  <td class="text-danger">- Rp {{ number_format($item->total_potongan ?? 0, 0, ',', '.') }}</td>
                  <td class="text-danger">- Rp {{ number_format($item->pajak_pph21 ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <strong class="text-success">Rp {{ number_format($item->gaji_bersih ?? 0, 0, ',', '.') }}</strong>
                  </td>
                  <td>
                    @if($item->tanggal_transfer)
                      <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal_transfer)->translatedFormat('d M Y') }}</small>
                    @else
                      <small class="text-muted">-</small>
                    @endif
                  </td>
                  <td>
                    @if($item->status == 'paid')
                      <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                    @elseif($item->status == 'pending')
                      <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending</span>
                    @else
                      <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('officers.penggajian.show', $item->id_penggajian) }}" class="btn btn-sm btn-outline-info">
                      <i class="bi bi-eye"></i>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        @elseif($periodeFilter)
          {{-- Filter aktif tapi data kosong --}}
          <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size:3rem"></i>
            <p class="text-muted mt-2 mb-1">Tidak ada data untuk periode <strong>{{ $periodeLabel }}</strong>.</p>
            <p class="text-muted small">Klik <strong>Hitung Gaji</strong> untuk memproses penggajian bulan ini.</p>
            <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalHitungGaji">
              <i class="bi bi-calculator"></i> Hitung Gaji Sekarang
            </button>
          </div>
        @else
          <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size:3rem"></i>
            <p class="text-muted mt-2">Belum ada data penggajian.</p>
            <p class="text-muted small">Pilih periode dan klik <strong>Hitung Gaji</strong>.</p>
          </div>
        @endif

      </div>
    </div>
  </div>
</div>

{{-- Modal: Konfirmasi Hitung Gaji --}}
<div class="modal fade" id="modalHitungGaji" tabindex="-1" aria-labelledby="modalHitungGajiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('officers.penggajian.calculate') }}">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalHitungGajiLabel">
            <i class="bi bi-calculator me-2"></i>Hitung Gaji Bulanan
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3">Pilih bulan untuk menghitung penggajian pegawai di departemen Anda. Gaji akan ditransfer di <strong>akhir bulan</strong> yang dipilih.</p>
          <div class="mb-3">
            <label for="modalPeriode" class="form-label fw-semibold">Periode Penggajian</label>
            <input type="month" id="modalPeriode" name="periode" class="form-control"
              value="{{ $periodeFilter ?? now()->format('Y-m') }}" required>
            <div class="form-text">Contoh: pilih Maret 2026 → gaji dihitung 01–31 Maret, transfer 31 Maret 2026</div>
          </div>
          <div class="alert alert-warning border-0 py-2">
            <i class="bi bi-info-circle me-1"></i>
            Pegawai yang sudah dihitung untuk periode ini akan dilewati secara otomatis.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-play-circle me-1"></i>Jalankan Hitung Gaji
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@extends('layouts.app')

@section('title', 'Data Penggajian')
@section('description', 'Kelola Data Penggajian Bulanan')

@section('content')
<div class="row mb-3">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h4 class="card-title mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>Data Penggajian Bulanan</h4>
          <small class="text-muted">Super Admin — Hanya lihat data. Hitung gaji dilakukan oleh HR Officer.</small>
        </div>
        {{-- Filter Periode --}}
        <form method="GET" action="{{ route('administrators.penggajian.index') }}" class="d-flex gap-2 align-items-center">
          <input type="month" name="periode" class="form-control form-control-sm"
            value="{{ $periodeFilter }}"
            onchange="this.form.submit()">
          @if($periodeFilter)
            <a href="{{ route('administrators.penggajian.index') }}" class="btn btn-outline-secondary btn-sm text-nowrap">
              <i class="bi bi-x-circle"></i> Reset
            </a>
          @endif
        </form>
      </div>

      <div class="card-body">

        {{-- Hanya tampilkan info periode yang dipilih jika ada filter --}}
        @if($periodeFilter && $periodeLabel)
          <div class="alert alert-info border-0 py-2 mb-3">
            <i class="bi bi-calendar-month me-2"></i>
            Menampilkan data penggajian bulan: <strong>{{ $periodeLabel }}</strong>
          </div>
        @endif

        @if($penggajian->count() > 0)

          {{-- Ringkasan Stats --}}
          @php
            $totalPaid    = $penggajian->where('status','paid')->sum('gaji_bersih');
            $totalPending = $penggajian->where('status','pending')->count();
            $totalPegawai = $penggajian->pluck('id_pegawai')->unique()->count();
          @endphp
          <div class="row mb-3 g-3">
            <div class="col-md-4">
              <div class="bg-success bg-opacity-10 rounded p-3 text-center border border-success border-opacity-25">
                <div class="fw-bold text-success fs-5">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <small class="text-muted">Total Gaji Terbayar</small>
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
                <div class="fw-bold text-primary fs-5">{{ $totalPegawai }}</div>
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
                      <span class="text-muted small">{{ \Carbon\Carbon::parse($item->tanggal_transfer)->translatedFormat('d M Y') }}</span>
                    @else
                      <span class="text-muted small">-</span>
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
                    <a href="{{ route('administrators.penggajian.show', $item->id_penggajian) }}" class="btn btn-sm btn-outline-info">
                      <i class="bi bi-eye"></i> Detail
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        @elseif($periodeFilter)
          {{-- Hanya tampilkan pesan kosong jika sudah filter tapi tidak ada data --}}
          <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size:3rem"></i>
            <p class="text-muted mt-2 mb-1">Tidak ada data penggajian untuk periode <strong>{{ $periodeLabel }}</strong>.</p>
            <p class="text-muted small">Hubungi HR Officer untuk memproses penggajian bulan tersebut.</p>
          </div>
        @else
          {{-- Belum pilih periode: tampilkan semua data atau pilih filter --}}
          <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size:3rem"></i>
            <p class="text-muted mt-2">Belum ada data penggajian.</p>
          </div>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection

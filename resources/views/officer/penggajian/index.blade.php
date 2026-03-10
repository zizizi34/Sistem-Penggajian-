@extends('layouts.app')

@section('title', 'Data Penggajian')
@section('description', 'HR Officer — Kelola Penggajian Seluruh Pegawai Perusahaan')

@section('content')
<div class="row mb-3">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h4 class="card-title mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>Penggajian Bulanan</h4>
          <small class="text-muted">
            <span class="badge bg-success me-1" style="font-size: 0.7rem;"><i class="bi bi-shield-check"></i> HR ACCESS</span>
            Kelola gaji lintas departemen
          </small>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <form method="GET" action="{{ route('officers.penggajian.index') }}" class="d-flex gap-2 align-items-center">
            {{-- Filter Departemen --}}
            <select name="departemen_id" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Semua Dept</option>
              @foreach($departemens as $dept)
                <option value="{{ $dept->id_departemen }}" {{ request('departemen_id') == $dept->id_departemen ? 'selected' : '' }}>
                  {{ $dept->nama_departemen }}
                </option>
              @endforeach
            </select>
            
            {{-- Filter Periode --}}
            <input type="month" name="periode" id="filterPeriode" class="form-control form-control-sm"
              value="{{ $periodeFilter }}"
              onchange="this.form.submit()">
              
            @if($periodeFilter || request('departemen_id'))
              <a href="{{ route('officers.penggajian.index') }}" class="btn btn-outline-secondary btn-sm text-nowrap">
                <i class="bi bi-x-circle"></i> Reset
              </a>
            @endif
          </form>

          <button type="button" class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalHitungGaji">
            <i class="bi bi-calculator"></i> Hitung Gaji
          </button>
        </div>
      </div>

      <div class="card-body">
        @if($periodeFilter && $periodeLabel)
          <div class="alert alert-info border-0 py-2 mb-3 d-flex justify-content-between align-items-center flex-wrap" style="font-size: 0.9rem;">
            <span><i class="bi bi-info-circle-fill me-2"></i>Menampilkan data periode: <strong>{{ $periodeLabel }}</strong></span>
            
            {{-- Tombol Bayar Masal jika ada pending --}}
            @if($penggajian->where('status', 'pending')->count() > 0)
              <form action="{{ route('officers.penggajian.bulk-pay') }}" method="POST" class="ms-auto mt-2 mt-md-0 d-inline form-bulk-pay">
                @csrf
                <input type="hidden" name="periode" value="{{ request('periode') }}">
                <input type="hidden" name="departemen_id" value="{{ request('departemen_id') }}">
                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm border-0 btn-bulk-pay">
                    <i class="bi bi-check2-all me-1"></i> Bayar Masal
                </button>
              </form>
            @endif
          </div>
        @endif

        @if($penggajian->count() > 0)
          @php
            $totalBersih  = $penggajian->sum('gaji_bersih');
            $totalPending = $penggajian->where('status','pending')->count();
          @endphp
          <div class="row mb-4 g-3">
            <div class="col-6 col-md-4">
              <div class="card border border-success border-opacity-25 bg-success bg-opacity-10 mb-0">
                <div class="card-body p-3 text-center">
                  <div class="text-success fw-bold" style="font-size: 1.1rem;">Rp {{ number_format($totalBersih, 0, ',', '.') }}</div>
                  <div class="text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Total Gaji Bersih</div>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-4">
              <div class="card border border-warning border-opacity-25 bg-warning bg-opacity-10 mb-0">
                <div class="card-body p-3 text-center">
                  <div class="text-warning fw-bold" style="font-size: 1.1rem;">{{ $totalPending }}</div>
                  <div class="text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Pending Bayar</div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="card border border-primary border-opacity-25 bg-primary bg-opacity-10 mb-0">
                <div class="card-body p-3 text-center">
                  <div class="text-primary fw-bold" style="font-size: 1.1rem;">{{ $penggajian->count() }}</div>
                  <div class="text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Total Record</div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle border-top" style="font-size: 0.85rem;">
              <thead class="table-light">
                <tr>
                  <th style="min-width: 180px;">Pegawai</th>
                  <th class="text-center">Periode</th>
                  <th>Gaji Pokok & Tunjangan</th>
                  <th>Lembur & Potongan</th>
                  <th>Gaji Bersih</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($penggajian as $item)
                <tr>
                  <td>
                    <div class="fw-bold text-dark">{{ $item->pegawai->nama_pegawai ?? '-' }}</div>
                    <div class="text-muted small">{{ $item->pegawai->departemen->nama_departemen ?? '-' }}</div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $item->periode }}</span>
                  </td>
                  <td>
                    <div class="text-dark">P: Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</div>
                    <div class="text-success small">+ T: Rp {{ number_format($item->total_tunjangan, 0, ',', '.') }}</div>
                  </td>
                  <td>
                    <div class="text-info">L: Rp {{ number_format($item->lembur, 0, ',', '.') }}</div>
                    <div class="text-danger small">- P: Rp {{ number_format($item->total_potongan + $item->pajak_pph21, 0, ',', '.') }}</div>
                  </td>
                  <td>
                    <div class="fw-bold text-primary" style="font-size: 1rem;">Rp {{ number_format($item->gaji_bersih, 0, ',', '.') }}</div>
                    <div class="text-muted small" style="font-size: 0.7rem;">Trf: {{ $item->tanggal_transfer ? \Carbon\Carbon::parse($item->tanggal_transfer)->format('d/m/Y') : '-' }}</div>
                  </td>
                  <td class="text-center">
                    @if($item->status == 'paid')
                      <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>PAID</span>
                    @else
                      <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-clock me-1"></i>PENDING</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="{{ route('officers.penggajian.show', $item->id_penggajian) }}" class="btn btn-sm btn-light border" title="Lihat Slip">
                          <i class="bi bi-eye text-primary"></i>
                        </a>
                        
                        @if($item->status == 'pending')
                        <form action="{{ route('officers.penggajian.pay', $item->id_penggajian) }}" method="POST" class="form-pay">
                            @csrf
                            <button type="button" class="btn btn-sm btn-success shadow-xs border-0 btn-single-pay" title="Tandai Sudah Bayar">
                                <i class="bi bi-check2"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        @elseif($periodeFilter)
          <div class="text-center py-5">
            <div class="mb-3"><i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i></div>
            <h5 class="text-muted">Data Kosong</h5>
            <p class="text-muted small">Tidak ada penggajian untuk periode <strong>{{ $periodeLabel }}</strong>.</p>
            <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalHitungGaji">
              <i class="bi bi-plus-circle me-1"></i> Mulai Hitung
            </button>
          </div>
        @else
          <div class="text-center py-5">
            <div class="mb-3"><i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i></div>
            <h5 class="text-muted">Pilih Periode</h5>
            <p class="text-muted small">Silakan pilih bulan/periode untuk melihat data.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="modalHitungGaji" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('officers.penggajian.calculate') }}">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-calculator me-2"></i>Hitung Gaji Bulanan</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning border-0 d-flex align-items-center" style="font-size: 0.85rem;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>Sistem hanya akan menghitung gaji jika <strong>data absensi</strong> di bulan tersebut sudah tersedia.</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold small">Periode Penggajian <span class="text-danger">*</span></label>
            <input type="month" name="periode" class="form-control" value="{{ now()->format('Y-m') }}" required>
            <div class="form-text small">Pilih bulan yang ingin diproses (tidak boleh bulan depan).</div>
          </div>
          <div class="mb-0">
            <label class="form-label fw-bold small">Filter Departemen (Opsional)</label>
            <select name="departemen_id" class="form-select">
              <option value="">-- Semua Departemen --</option>
              @foreach($departemens as $dept)
                <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light p-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-play-fill me-1"></i> Jalankan Hitung
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('script')
<script>
    $(function() {
        // Pop-up untuk bayar satu per satu
        $('.btn-single-pay').click(function(e) {
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: "Tandai gaji pegawai ini sebagai PAID (Sudah Dibayar)?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Sudah Bayar',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Pop-up untuk bayar masal
        $('.btn-bulk-pay').click(function(e) {
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Bayar Masal?',
                text: "Semua data penggajian yang tampil akan diubah statusnya menjadi PAID. Pastikan transfer sudah dilakukan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check2-all me-1"></i> Ya, Bayar Semua!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

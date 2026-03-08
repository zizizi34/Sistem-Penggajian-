@extends('layouts.app')

@section('title', 'Absensi Saya')
@section('description', 'Halaman Absensi Harian Pegawai')

@section('content')

{{-- ===================== ALERTS ===================== --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible auto-dismiss fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible auto-dismiss fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ===================== STATUS HARI INI + STATISTIK ===================== --}}
<div class="row mb-4 g-3">
    {{-- Status Kehadiran Hari Ini --}}
    <div class="col-12 col-lg-5">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-4 p-4">
                <div class="flex-grow-1">
                    <p class="text-muted mb-1 fs-6">Status Kehadiran Hari Ini</p>
                    @if($attendance)
                        @if(in_array($attendance->status, ['hadir', 'terlambat']))
                            <h4 class="fw-bold text-success mb-1">
                                {{ ucfirst($attendance->status) }}
                            </h4>
                        @elseif($attendance->status === 'izin')
                            <h4 class="fw-bold text-info mb-1">Izin</h4>
                        @elseif($attendance->status === 'sakit')
                            <h4 class="fw-bold text-warning mb-1">Sakit</h4>
                        @else
                            <h4 class="fw-bold text-secondary mb-1">{{ ucfirst($attendance->status) }}</h4>
                        @endif
                        <small class="text-muted d-none">
                            Masuk: <strong>{{ $attendance->jam_masuk ?? '-' }}</strong>
                            &nbsp;|&nbsp;
                            Pulang: <strong>{{ $attendance->jam_pulang ?? '-' }}</strong>
                        </small>
                    @else
                        <h4 class="fw-bold text-secondary mb-1">
                            Belum Hadir
                        </h4>
                    @endif
                </div>
                <div>
                    @if(!$attendance || ($attendance->jam_masuk && !$attendance->jam_pulang))
                        @php $type = !$attendance ? 'masuk' : 'pulang'; @endphp
                        <form action="{{ route('students.attendance.store') }}" method="POST" enctype="multipart/form-data" id="fast-absen-form">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <label for="foto-absen" class="btn btn-{{ !$attendance ? 'success' : 'warning' }} px-4 py-2 m-0 shadow-sm" style="cursor: pointer; font-weight: 500; font-size: 15px;">
                                <i class="bi bi-fingerprint me-1"></i> Absen {{ !$attendance ? 'Sekarang' : 'Pulang' }}
                            </label>
                            <input type="file" name="foto" id="foto-absen" accept="image/*" class="d-none" onchange="document.getElementById('fast-absen-form').submit()">
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="col-12 col-lg-7">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="row g-0 w-100 text-center">
                    <div class="col-4 border-end">
                        <div class="p-2 py-0 text-start">
                            <span class="text-dark fs-6 d-block mb-2">Total Kehadiran</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $totalHadir }}</h3>
                        </div>
                    </div>
                    <div class="col-4 border-end px-3">
                        <div class="p-2 py-0 text-start">
                            <span class="text-dark fs-6 d-block mb-2">Total Tidak Masuk</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $totalTidakMasuk }}</h3>
                        </div>
                    </div>
                    <div class="col-4 ps-3">
                        <div class="p-2 py-0 text-start">
                            <span class="text-dark fs-6 d-block mb-2">Total Izin</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $totalIzin }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- (Form Absen disembunyikan karena sudah menggunakan auto-submit button di atas) --}}

{{-- ===================== TABEL RIWAYAT ABSENSI ===================== --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white fw-semibold py-3">
                <i class="bi bi-table me-2"></i>Kehadiran dan Laporan Anggota
            </div>
            <div class="card-body p-4">
                {{-- Filter --}}
                <form method="GET" action="{{ route('students.attendance.index') }}" id="form-filter">
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-12 col-sm-4 col-lg-3">
                            <label class="form-label fw-semibold">Dari</label>
                            <input type="date" name="tanggal_dari" id="filter-dari"
                                class="form-control"
                                value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-12 col-sm-4 col-lg-3">
                            <label class="form-label fw-semibold">Sampai</label>
                            <input type="date" name="tanggal_sampai" id="filter-sampai"
                                class="form-control"
                                value="{{ request('tanggal_sampai') }}">
                        </div>
                        <div class="col-12 col-sm-4 col-lg-3">
                            <label class="form-label fw-semibold">Filter Status</label>
                            <select name="status" id="filter-status" class="form-select">
                                <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua</option>
                                <option value="hadir"     {{ request('status') === 'hadir'     ? 'selected' : '' }}>Hadir</option>
                                <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                <option value="izin"      {{ request('status') === 'izin'      ? 'selected' : '' }}>Izin</option>
                                <option value="sakit"     {{ request('status') === 'sakit'     ? 'selected' : '' }}>Sakit</option>
                                <option value="alpha"     {{ request('status') === 'alpha'     ? 'selected' : '' }}>Alpha</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="btn-filter">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                            @if(request()->hasAny(['tanggal_dari','tanggal_sampai','status']))
                                <a href="{{ route('students.attendance.index') }}" class="btn btn-outline-secondary" id="btn-reset-filter">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Tabel Riwayat --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabel-absensi">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Tanggal</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Jam Masuk</th>
                                <th class="text-nowrap">Jam Pulang</th>
                                <th class="text-nowrap">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                <tr>
                                    <td class="fw-semibold text-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal_absensi)->translatedFormat('d M Y') }}
                                    </td>
                                    <td>
                                        @if($item->status === 'hadir')
                                            <span class="badge bg-success px-3 py-2">Hadir</span>
                                        @elseif($item->status === 'terlambat')
                                            <span class="badge bg-warning text-dark px-3 py-2">Terlambat</span>
                                        @elseif($item->status === 'izin')
                                            <span class="badge bg-info px-3 py-2">Izin</span>
                                        @elseif($item->status === 'sakit')
                                            <span class="badge bg-primary px-3 py-2">Sakit</span>
                                        @elseif($item->status === 'alpha')
                                            <span class="badge bg-danger px-3 py-2">Alpha</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jam_masuk)
                                            <span class="text-success fw-semibold">
                                                <i class="bi bi-box-arrow-in-right me-1"></i>{{ $item->jam_masuk }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jam_pulang)
                                            <span class="text-warning fw-semibold">
                                                <i class="bi bi-box-arrow-left me-1"></i>{{ $item->jam_pulang }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $item->keterangan ?: '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4 border-0">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        Belum ada riwayat absensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($history->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $history->firstItem() }}–{{ $history->lastItem() }} dari {{ $history->total() }} data
                    </small>
                    {{ $history->links('pagination::bootstrap-5') }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    // Auto submit filter saat select berubah
    document.getElementById('filter-status').addEventListener('change', function() {
        document.getElementById('form-filter').submit();
    });

    // Auto dismiss alerts setelah 5 detik
    setTimeout(function() {
        document.querySelectorAll('.auto-dismiss').forEach(function(el) {
            let bsAlert = new bootstrap.Alert(el);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush

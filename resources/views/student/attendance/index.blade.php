@extends('layouts.app')

@section('title', 'Absensi Saya')
@section('description', 'Halaman Absensi Harian Pegawai')

@section('content')
<style>
    /* Premium Dashboard Utilities */
    .fw-extrabold { font-weight: 800 !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1) !important; color: #198754 !important; }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1) !important; color: #dc3545 !important; }
    .bg-light-info { background-color: rgba(13, 202, 240, 0.1) !important; color: #0dcaf0 !important; }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1) !important; color: #ffc107 !important; }
    
    .stats-icon {
        width: 64px !important;
        height: 64px !important;
        border-radius: 1.25rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 2.2rem !important;
        margin: 0 auto 1.25rem auto !important;
        transition: all 0.3s ease;
        box-shadow: 0 8px 15px -3px rgba(0,0,0,0.07);
    }
    .stats-icon i {
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: normal;
    }
    .stats-icon.success { background: linear-gradient(135deg, #198754, #28a745); color: white; }
    .stats-icon.danger { background: linear-gradient(135deg, #dc3545, #ff4d5e); color: white; }
    .stats-icon.info { background: linear-gradient(135deg, #0dcaf0, #31d2f2); color: white; }
    
    .stats-card-inner:hover .stats-icon {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0,0,0,0.15);
    }
    
    .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important; }
    
    .opacity-10 { opacity: 0.1; }
    
    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6c757d;
        border-top: none;
    }
    
    .filter-card {
        background: #ffffff;
        border-radius: 1rem;
    }
    
    .form-control, .form-select {
        border-radius: 0.6rem;
        padding: 0.6rem 1rem;
        border-color: #e9ecef;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.1);
    }
</style>

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

@if(isset($overtimeNotification))
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 p-4 animate__animated animate__headShake" role="alert">
  <div class="stats-icon orange me-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255, 153, 0, 0.15); border-radius: 12px; flex-shrink: 0;">
    <i class="bi bi-megaphone-fill text-warning fs-4"></i>
  </div>
  <div>
    <h6 class="alert-heading fw-bold text-dark mb-1">Pemberitahuan Lembur Hari Ini!</h6>
    <p class="mb-0 text-muted small">
      <strong>Catatan Petugas:</strong> "{{ $overtimeNotification->keterangan ?: 'Segera selesaikan tugas yang diberikan.' }}"
    </p>
  </div>
</div>
@endif

{{-- ===================== STATUS HARI INI + STATISTIK ===================== --}}
<div class="row mb-4 g-4">
    {{-- Status Kehadiran Hari Ini --}}
    <div class="col-12 col-xl-4 col-lg-5">
        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 1.25rem;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-uppercase tracking-wider fw-bold text-muted small">Status Hari Ini</span>
                            @if($isClosed)
                                <span class="badge bg-light-danger text-danger border-0 px-2 py-1 small rounded-pill">
                                    <i class="bi bi-lock-fill me-1"></i> Ditutup
                                </span>
                            @endif
                        </div>
                        
                        @if($attendance)
                            @php
                                $statusClass = match(strtolower($attendance->status)) {
                                    'hadir', 'lembur' => 'success',
                                    'terlambat', 'lembur tetapi lupa absen pulang' => 'warning',
                                    'izin', 'sakit' => 'info',
                                    'alpha' => 'danger',
                                    default => 'secondary'
                                };
                                $isPresentToday = in_array(strtolower($attendance->status), ['hadir', 'terlambat', 'lembur', 'pulang cepat', 'lupa absen pulang', 'lembur tetapi lupa absen pulang']) && !is_null($attendance->jam_masuk);
                                
                                if ($isPresentToday) {
                                    $displayName = 'Hadir';
                                } elseif (in_array(strtolower($attendance->status), ['izin', 'sakit'])) {
                                    $displayName = ucfirst($attendance->status);
                                } else {
                                    $displayName = 'Tidak hadir';
                                }
                            @endphp
                            <h2 class="fw-extrabold text-{{ $statusClass }} mb-2">
                                {{ $displayName }}
                            </h2>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-clock me-2"></i>
                                <span>{{ $attendance->jam_masuk }}</span>
                                <i class="bi bi-arrow-right mx-2 text-light"></i>
                                <span>{{ $attendance->jam_pulang ?? '--:--' }}</span>
                            </div>
                        @else
                            <h2 class="fw-extrabold text-secondary mb-2">Belum Hadir</h2>
                            <p class="text-muted small mb-0">Silakan lakukan absensi masuk</p>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top border-light">
                        @if(!$isClosed && (!$attendance || ($attendance->jam_masuk && !$attendance->jam_pulang)))
                            @php $type = !$attendance ? 'masuk' : 'pulang'; @endphp
                            <form action="{{ route('students.attendance.store') }}" method="POST" enctype="multipart/form-data" id="fast-absen-form">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type }}">
                                <input type="file" name="foto" id="foto-absen" accept="image/*" class="d-none" onchange="document.getElementById('fast-absen-form').submit()">
                                <label for="foto-absen" class="btn btn-{{ !$attendance ? 'success' : 'warning' }} w-100 py-2 rounded-3 shadow-sm fw-bold">
                                    <i class="bi bi-fingerprint me-2"></i> Absen {{ !$attendance ? 'Masuk' : 'Pulang' }}
                                </label>
                            </form>
                        @elseif($isClosed)
                             <div class="alert alert-light border-0 mb-0 py-2 px-3 small text-center rounded-3 bg-light">
                                <i class="bi bi-info-circle me-1 text-danger"></i> Batas waktu absensi hari ini berakhir.
                             </div>
                        @else
                             <div class="alert alert-light border-0 mb-0 py-2 px-3 small text-center rounded-3 bg-light">
                                <i class="bi bi-check-circle-fill me-1 text-success"></i> Absensi hari ini telah lengkap.
                             </div>
                        @endif
                    </div>
                </div>
                {{-- Subtle BG Icon --}}
                <i class="bi bi-calendar-check position-absolute bottom-0 end-0 mb-n3 me-n2 opacity-10" style="font-size: 8rem; color: var(--bs-primary);"></i>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="col-12 col-xl-8 col-lg-7">
        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 1.25rem;">
            <div class="card-body p-4">
                <p class="text-uppercase tracking-wider fw-bold text-muted small mb-4 text-center">Ringkasan {{ $currentMonthName ?? 'Bulan Ini' }}</p>
                <div class="row g-4 h-100 align-items-center">
                    <div class="col-4">
                        <div class="stats-card-inner p-3 rounded-4 bg-light-success h-100 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="stats-icon success">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <h2 class="fw-extrabold mb-1">{{ $totalHadir }}</h2>
                            <span class="text-muted fw-bold small">Kehadiran</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-card-inner p-3 rounded-4 bg-light-danger h-100 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="stats-icon danger">
                                <i class="bi bi-person-x-fill"></i>
                            </div>
                            <h2 class="fw-extrabold mb-1">{{ $totalTidakMasuk }}</h2>
                            <span class="text-muted fw-bold small">Alpha</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-card-inner p-3 rounded-4 bg-light-info h-100 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="stats-icon info">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <h2 class="fw-extrabold mb-1">{{ $totalIzin }}</h2>
                            <span class="text-muted fw-bold small">Izin/Sakit</span>
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
        <div class="card border-0 shadow-sm" style="border-radius: 1.25rem;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Riwayat Kehadiran</h5>
                    <div class="text-muted small">
                         <i class="bi bi-info-circle me-1"></i> Data diurutkan berdasarkan tanggal terbaru
                    </div>
                </div>

                {{-- Filter --}}
                <div class="filter-card p-3 mb-4 border border-light bg-light bg-opacity-10" style="border-radius: 1rem;">
                    <form method="GET" action="{{ route('students.attendance.index') }}" id="form-filter">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Dari Tanggal</label>
                                <input type="date" name="tanggal_dari" id="filter-dari"
                                    class="form-control"
                                    value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Sampai Tanggal</label>
                                <input type="date" name="tanggal_sampai" id="filter-sampai"
                                    class="form-control"
                                    value="{{ request('tanggal_sampai') }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Status</label>
                                <select name="status" id="filter-status" class="form-select">
                                    <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="tidak_hadir" {{ request('status') === 'tidak_hadir' ? 'selected' : '' }}>Tidak hadir</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 rounded-3 w-100 fw-bold">
                                    <i class="bi bi-funnel-fill me-2"></i>Filter
                                </button>
                                @if(request()->hasAny(['tanggal_dari','tanggal_sampai','status']))
                                    <a href="{{ route('students.attendance.index') }}" class="btn btn-light px-3 rounded-3 border">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tabel Riwayat --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0" id="tabel-absensi">
                        <thead>
                            <tr>
                                <th class="ps-0">Tanggal</th>
                                <th>Status</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th class="pe-0">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                <tr>
                                    <td class="ps-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-2 p-2 me-3 text-center" style="min-width: 50px;">
                                                <span class="d-block fw-bold text-dark lh-1">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->format('d') }}</span>
                                                <span class="d-block text-muted small text-uppercase" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->translatedFormat('M') }}</span>
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->translatedFormat('l') }}</span>
                                                <span class="text-muted small">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->format('Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $hasMasuk = !is_null($item->jam_masuk);
                                            $isPresent = in_array(strtolower($item->status), ['hadir', 'terlambat', 'lembur', 'pulang cepat', 'lupa absen pulang', 'lembur tetapi lupa absen pulang']) && $hasMasuk;
                                            
                                            if ($isPresent) {
                                                $statusText = 'Hadir';
                                                $statusClass = 'bg-light-success text-success';
                                            } elseif (in_array(strtolower($item->status), ['izin', 'sakit'])) {
                                                $statusText = ucfirst($item->status);
                                                $statusClass = 'bg-light-info text-info';
                                            } else {
                                                $statusText = 'Tidak hadir';
                                                $statusClass = 'bg-light-danger text-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->jam_masuk)
                                            <div class="d-flex align-items-center text-success fw-bold">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>{{ $item->jam_masuk }}
                                            </div>
                                        @else
                                            <span class="text-muted small">---</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jam_pulang)
                                            <div class="d-flex align-items-center text-warning fw-bold">
                                                <i class="bi bi-box-arrow-left me-2"></i>{{ $item->jam_pulang }}
                                            </div>
                                        @else
                                            <span class="text-muted small">---</span>
                                        @endif
                                    </td>
                                    <td class="pe-0">
                                        @php
                                            $remarks = [];
                                            
                                            // 1. Deteksi Terlambat (Berdasarkan Jam Masuk vs Jadwal)
                                            if($isPresent && $jadwal && $item->jam_masuk) {
                                                $jamMasuk = \Carbon\Carbon::parse($item->jam_masuk);
                                                $batasMasuk = \Carbon\Carbon::parse($jadwal->jam_masuk)->addMinutes($jadwal->toleransi_terlambat ?? 0);
                                                if($jamMasuk->greaterThan($batasMasuk)) {
                                                    $remarks[] = 'Terlambat';
                                                }
                                            }
                                            
                                            // 2. Deteksi Pulang Cepat
                                            if($item->status === 'Pulang Cepat' || ($item->jam_pulang && $jadwal && \Carbon\Carbon::parse($item->jam_pulang)->lt(\Carbon\Carbon::parse($jadwal->jam_pulang)))) {
                                                $remarks[] = 'Pulang cepat';
                                            }
                                            
                                            // 3. Deteksi Lembur
                                            if(str_contains($item->status, 'Lembur')) {
                                                $remarks[] = 'Lembur';
                                            }
                                            
                                            // 4. Deteksi Lupa Absen Pulang (Jika tidak ada lembur)
                                            if(str_contains($item->status, 'Lupa Absen Pulang') && !str_contains($item->status, 'Lembur')) {
                                                $remarks[] = 'Lupa absen pulang';
                                            }
                                            
                                            // 5. Izin/Sakit (Sebagai catatan)
                                            if(!$isPresent && in_array($item->status, ['izin', 'sakit'])) {
                                                $remarks[] = ucfirst($item->status);
                                            }
                                            
                                            // Jika remarks kosong, tentukan teks default (Tepat Waktu vs Tanpa Keterangan)
                                            $defaultText = $isPresent ? 'Tepat Waktu' : ($item->keterangan ?: ucfirst($item->status));
                                            $displayKeterangan = !empty($remarks) ? implode(', ', $remarks) : $defaultText;
                                        @endphp
                                        <span class="text-muted small fw-bold">{{ $displayKeterangan }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="p-4 bg-light rounded-4 d-inline-block mb-3">
                                            <i class="bi bi-calendar-x text-muted fs-1"></i>
                                        </div>
                                        <h6 class="fw-bold text-muted">Tidak ada data</h6>
                                        <p class="text-muted small">Data absensi Anda akan muncul di sini setelah Anda melakukan absensi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($history->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <small class="text-muted">
                        Menampilkan <strong>{{ $history->firstItem() }}–{{ $history->lastItem() }}</strong> dari <strong>{{ $history->total() }}</strong> data
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

@extends('layouts.app')

@section('title', 'Absensi Saya')
@section('description', 'Halaman Absensi Harian Pegawai')

@section('content')
<style>
    /* Premium Dashboard Utilities */
    :root {
        --primary-gradient: linear-gradient(135deg, #1C352D 0%, #2D5A4C 100%);
        --success-gradient: linear-gradient(135deg, #198754 0%, #28a745 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        --danger-gradient: linear-gradient(135deg, #dc3545 0%, #ff4d5e 100%);
        --info-gradient: linear-gradient(135deg, #0dcaf0 0%, #31d2f2 100%);
        --glass-bg: rgba(255, 255, 255, 0.9);
    }

    .fw-extrabold { font-weight: 800 !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    
    /* Elegant Cards */
    .premium-card {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        overflow: hidden;
    }
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    /* Stats Icons */
    .stats-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .stats-icon-wrapper.success { background: rgba(25, 135, 84, 0.1); color: #198754; }
    .stats-icon-wrapper.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .stats-icon-wrapper.info { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
    .stats-icon-wrapper.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }

    .premium-card:hover .stats-icon-wrapper {
        transform: scale(1.1);
    }

    /* Status Badges */
    .badge-pill-custom {
        padding: 0.5rem 1.25rem;
        border-radius: 50rem;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* Overtime Alert */
    .overtime-notice {
        background: linear-gradient(to right, #fffbeb, #ffffff);
        border-left: 5px solid #f59e0b;
        border-radius: 1rem;
    }

    /* Table Styles */
    .table-premium thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #f1f5f9;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1.25rem 1rem;
    }
    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    /* Action Buttons */
    .btn-action {
        border-radius: 1rem;
        padding: 0.8rem 1.5rem;
        font-weight: 700;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        line-height: normal;
    }
    .btn-action i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }
    .btn-action-success { background: var(--success-gradient); color: white; }
    .btn-action-warning { background: var(--warning-gradient); color: #92400e; }
    .btn-action:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        filter: brightness(1.1);
    }

    /* Glass Text */
    .text-glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
    }

    /* Text & Icon Perfect Alignment */
    .align-icon-text {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        line-height: normal;
    }
    .align-icon-text i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }
    .stretch-row {
        display: flex;
        align-items: stretch;
    }
    .stretch-row > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }
    
    /* Icon Centering Fix */
    .center-icon {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 0 !important;
    }
</style>

{{-- ===================== ALERTS ===================== --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible auto-dismiss fade show mb-4" role="alert" style="border-radius: 1rem; background: #ecfdf5; color: #065f46;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible auto-dismiss fade show mb-4" role="alert" style="border-radius: 1rem; background: #fef2f2; color: #991b1b;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(isset($overtimeNotification))
<div class="overtime-notice shadow mb-4 border border-warning border-opacity-20 rounded-4 overflow-hidden bg-white" role="alert">
  <div class="row g-0 align-items-center">
    <div class="col-auto">
        <div class="bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
            <i class="bi bi-megaphone-fill text-warning center-icon" style="font-size: 2.5rem;"></i>
        </div>
    </div>
    <div class="col px-5 py-4">
        <h5 class="fw-bold text-dark mb-2">Pemberitahuan Lembur Hari Ini!</h5>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark py-2 px-3" style="font-size: 0.7rem; font-weight: 800; border-radius: 0.5rem;">CATATAN</span>
            <p class="mb-0 text-muted lh-sm fw-medium" style="font-size: 1rem;">"{{ $overtimeNotification->keterangan ?: 'Segera selesaikan tugas yang diberikan.' }}"</p>
        </div>
    </div>
  </div>
</div>
@endif

{{-- ===================== TOP SECTION: STATUS & RINGKASAN ===================== --}}
<div class="row mb-4 g-4 stretch-row">
    {{-- Card Status Hari Ini --}}
    <div class="col-lg-5">
        <div class="premium-card h-100 border-0 shadow-sm" style="background: white; border-radius: 1.25rem; border-left: 5px solid #1C352D !important;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-check text-muted small"></i>
                            <span class="text-uppercase small fw-bold text-muted tracking-widest" style="font-size: 0.7rem;">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
                        </div>
                        @if(!$isClosed)
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small fw-bold border border-success border-opacity-10" style="font-size: 0.65rem;">ONLINE</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill small fw-bold border border-danger border-opacity-10" style="font-size: 0.65rem;">CLOSED</span>
                        @endif
                    </div>

                    @if($attendance)
                        @php
                            $statusClass = match(strtolower($attendance->status)) {
                                'hadir', 'lembur' => 'success',
                                'terlambat' => 'warning',
                                'izin', 'sakit' => 'info',
                                'alpha' => 'danger',
                                default => 'secondary'
                            };
                            $isPresent = in_array(strtolower($attendance->status), ['hadir', 'terlambat', 'lembur', 'pulang cepat', 'lupa absen pulang', 'lembur tetapi lupa absen pulang']);
                        @endphp
                        <div class="mb-4">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1 opacity-75" style="letter-spacing: 1px; font-size: 0.65rem;">Kehadiran Hari Ini:</span>
                            <h2 class="fw-extrabold text-{{ $statusClass }} mb-4" style="font-size: 2.5rem;">{{ $isPresent ? 'Hadir' : ucfirst($attendance->status) }}</h2>
                            
                            <div class="p-3 bg-light rounded-4 border border-white">
                                <div class="row align-items-center g-0">
                                    <div class="col-auto me-3">
                                        <div class="bg-{{ $statusClass }} bg-opacity-20 text-{{ $statusClass }} rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                                            <i class="bi bi-clock-history fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-uppercase text-muted small mb-0" style="font-size: 0.6rem; font-weight: 800;">Log Waktu</div>
                                        <div class="fw-bold text-dark fs-5 align-icon-text">
                                            <span>{{ $attendance->jam_masuk }}</span>
                                            <i class="bi bi-chevron-right text-silver mx-2" style="font-size: 0.8rem;"></i>
                                            <span>{{ $attendance->jam_pulang ?? '--:--' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1 opacity-75" style="letter-spacing: 1px; font-size: 0.65rem;">Kehadiran Hari Ini:</span>
                            <h2 class="fw-extrabold text-dark mb-4" style="font-size: 2.5rem;">Belum Absen</h2>
                            
                            <div class="p-3 bg-dark rounded-4 shadow-sm text-white">
                                <div class="row align-items-center g-0">
                                    <div class="col-auto me-3">
                                        <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="bi bi-clock fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-uppercase text-white-50 small mb-0" style="font-size: 0.6rem; font-weight: 800;">Waktu Lokal</div>
                                        <div id="real-time-clock" class="fw-bold fs-5 tracking-widest">00:00:00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if(!$isClosed && (!$attendance || ($attendance->jam_masuk && !$attendance->jam_pulang)))
                    @php $type = !$attendance ? 'masuk' : 'pulang'; @endphp
                    <form action="{{ route('students.attendance.store') }}" method="POST" enctype="multipart/form-data" id="fast-absen-form">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="file" name="foto" id="foto-absen" accept="image/*" class="d-none" onchange="document.getElementById('fast-absen-form').submit()">
                        <label for="foto-absen" class="btn w-100 p-3 rounded-4 fw-extrabold d-flex align-items-center justify-content-center gap-2 text-white border-0 shadow cursor-pointer text-uppercase" 
                               style="background: {{ !$attendance ? 'linear-gradient(135deg, #198754, #1c352d)' : 'linear-gradient(135deg, #f59e0b, #d97706)' }}; font-size: 0.9rem;">
                            <i class="bi bi-fingerprint fs-4 center-icon"></i>
                            <span class="d-inline-flex align-items-center">Absen {{ !$attendance ? 'Masuk' : 'Pulang' }} Sekarang</span>
                        </label>
                    </form>
                @elseif($isClosed)
                    <div class="p-3 bg-light rounded-4 text-center border text-muted fw-bold small">ABSENSI SUDAH DITUTUP</div>
                @else
                    <div class="p-3 bg-success bg-opacity-5 rounded-4 text-center border-success border-opacity-10 text-success fw-bold small">ABSENSI TUNTAS</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Ringkasan Statistik --}}
    <div class="col-lg-7">
        <div class="premium-card h-100 border-0 shadow-sm" style="background: white; border-radius: 1.25rem;">
            <div class="card-body p-4 p-xl-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-grid-fill text-muted" style="font-size: 0.9rem;"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Ringkasan {{ $currentMonthName }}</h5>
                </div>

                <div class="row g-3 mb-4">
                    {{-- Hadir --}}
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 d-flex flex-column align-items-center justify-content-center text-center h-100 border border-light" style="background: #f8fafc;">
                            <div class="bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-check fs-4 center-icon"></i>
                            </div>
                            <div class="fw-extrabold text-dark mb-0" style="font-size: 1.75rem;">{{ $totalHadir }}</div>
                            <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.6rem;">Hadir</div>
                        </div>
                    </div>
                    {{-- Alpha --}}
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 d-flex flex-column align-items-center justify-content-center text-center h-100 border border-light" style="background: #f8fafc;">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-4 d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-x fs-4 center-icon"></i>
                            </div>
                            <div class="fw-extrabold text-dark mb-0" style="font-size: 1.75rem;">{{ $totalTidakMasuk }}</div>
                            <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.6rem;">Alpha</div>
                        </div>
                    </div>
                    {{-- Izin --}}
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 d-flex flex-column align-items-center justify-content-center text-center h-100 border border-light" style="background: #f8fafc;">
                            <div class="bg-info bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-envelope fs-4 center-icon"></i>
                            </div>
                            <div class="fw-extrabold text-dark mb-0" style="font-size: 1.75rem;">{{ $totalIzin }}</div>
                            <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.6rem;">Izin/Sakit</div>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-4 border bg-light d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <div class="small text-muted fw-medium mb-0" style="font-size: 0.75rem;">
                        Data sinkron dengan sistem pusat untuk perhitungan payroll.
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
        <div class="premium-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1">Riwayat Kehadiran</h4>
                        <p class="text-muted small mb-0">Lihat performa kehadiran Anda dalam periode tertentu.</p>
                    </div>
                    <div class="text-muted small d-none d-md-block">
                         <i class="bi bi-sort-numeric-down me-1"></i> Terurut berdasarkan data terbaru
                    </div>
                </div>

                {{-- Filter --}}
                <div class="bg-light bg-opacity-50 p-4 mb-4" style="border-radius: 1.25rem; border: 1px solid #f1f5f9;">
                    <form method="GET" action="{{ route('students.attendance.index') }}" id="form-filter">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-2 fw-bold text-dark">Dari Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tanggal_dari" id="filter-dari" class="form-control border-start-0 ps-0" value="{{ request('tanggal_dari') }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-2 fw-bold text-dark">Sampai Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tanggal_sampai" id="filter-sampai" class="form-control border-start-0 ps-0" value="{{ request('tanggal_sampai') }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-2 fw-bold text-dark">Status</label>
                                <select name="status" id="filter-status" class="form-select">
                                    <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="tidak_hadir" {{ request('status') === 'tidak_hadir' ? 'selected' : '' }}>Tidak hadir</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold py-2">
                                    <i class="bi bi-search me-2"></i>Cari
                                </button>
                                @if(request()->hasAny(['tanggal_dari','tanggal_sampai','status']))
                                    <a href="{{ route('students.attendance.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 border">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tabel Riwayat --}}
                <div class="table-responsive">
                    <table class="table table-premium align-middle mb-0" id="tabel-absensi">
                        <thead>
                            <tr>
                                <th class="ps-4">Hari & Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Jam Masuk</th>
                                <th class="text-center">Jam Pulang</th>
                                <th class="pe-4">Keterangan Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3 d-flex flex-column align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <span class="fw-bold lh-1">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->format('d') }}</span>
                                                <span class="small text-uppercase" style="font-size: 0.6rem;">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->translatedFormat('M') }}</span>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->translatedFormat('l') }}</p>
                                                <span class="text-muted small">{{ \Carbon\Carbon::parse($item->tanggal_absensi)->format('Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $hasMasuk = !is_null($item->jam_masuk);
                                            $isPresent = in_array(strtolower($item->status), ['hadir', 'terlambat', 'lembur', 'pulang cepat', 'lupa absen pulang', 'lembur tetapi lupa absen pulang']) && $hasMasuk;
                                            
                                            if ($isPresent) {
                                                $statusText = 'Hadir';
                                                $statusClass = 'success';
                                            } elseif (in_array(strtolower($item->status), ['izin', 'sakit'])) {
                                                $statusText = ucfirst($item->status);
                                                $statusClass = 'info';
                                            } else {
                                                $statusText = 'Tidak hadir';
                                                $statusClass = 'danger';
                                            }
                                        @endphp
                                        <span class="badge-pill-custom bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->jam_masuk)
                                            <span class="fw-bold text-dark live-clock"><i class="bi bi-arrow-down-left-circle me-1 text-success"></i> {{ $item->jam_masuk }}</span>
                                        @else
                                            <span class="text-silver">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->jam_pulang)
                                            <span class="fw-bold text-dark live-clock"><i class="bi bi-arrow-up-right-circle me-1 text-warning"></i> {{ $item->jam_pulang }}</span>
                                        @else
                                            <span class="text-silver">—</span>
                                        @endif
                                    </td>
                                    <td class="pe-4">
                                        @php
                                            $remarks = [];
                                            if($isPresent && $jadwal && $item->jam_masuk) {
                                                $jamMasuk = \Carbon\Carbon::parse($item->jam_masuk);
                                                $batasMasuk = \Carbon\Carbon::parse($jadwal->jam_masuk)->addMinutes($jadwal->toleransi_terlambat ?? 0);
                                                if($jamMasuk->greaterThan($batasMasuk)) $remarks[] = 'Terlambat';
                                                else $remarks[] = 'Tepat Waktu';
                                            }
                                            if(strtolower($item->status) === 'pulang cepat' || ($item->jam_pulang && $jadwal && \Carbon\Carbon::parse($item->jam_pulang)->lt(\Carbon\Carbon::parse($jadwal->jam_pulang)))) $remarks[] = 'Pulang Cepat';
                                            if(str_contains(strtolower($item->status), 'lembur')) $remarks[] = 'Lembur';
                                            if(str_contains(strtolower($item->status), 'lupa absen pulang') && !str_contains(strtolower($item->status), 'lembur')) $remarks[] = 'Lupa Absen Pulang';
                                            if(!$isPresent && in_array(strtolower($item->status), ['izin', 'sakit'])) $remarks[] = ucfirst($item->status);
                                            
                                            $defaultText = $isPresent ? 'Tepat Waktu' : ($item->keterangan ?: ucfirst($item->status));
                                            $displayKeterangan = !empty($remarks) ? implode(', ', $remarks) : $defaultText;
                                        @endphp
                                        <div class="d-flex align-items-center gap-1">
                                            @foreach($remarks as $rem)
                                                <span class="badge bg-light text-dark border-0 small px-2 py-1" style="font-size: 0.65rem;">{{ $rem }}</span>
                                            @endforeach
                                            @if(empty($remarks))
                                                <span class="text-muted small">{{ $defaultText }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/amber/calendar.svg" alt="No data" style="width: 150px;" class="mb-4">
                                        <h5 class="fw-bold">Belum ada data absensi</h5>
                                        <p class="text-muted">Data absensi Anda akan ditampilkan di sini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($history->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                    <p class="text-muted mb-0 small">
                        Showing <strong>{{ $history->firstItem() }}</strong> to <strong>{{ $history->lastItem() }}</strong> of <strong>{{ $history->total() }}</strong> entries
                    </p>
                    <div class="pagination-premium">
                        {{ $history->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    // Real-time Clock Update
    function updateClock() {
        const element = document.getElementById('real-time-clock');
        if (!element) return;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: false 
        }).replace(/\./g, ':');
        
        element.textContent = timeString;
    }
    
    // Initial call and set interval
    updateClock();
    setInterval(updateClock, 1000);

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

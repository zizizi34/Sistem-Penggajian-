@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('description', 'Ikhtisar Manajemen Tim Anda')

@section('content')
<style>
    /* Professional Corporate Dashboard Styles */
    .dashboard-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    
    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .bg-light-primary { background-color: #eff6ff; color: #3b82f6; }
    .bg-light-success { background-color: #f0fdf4; color: #22c55e; }
    .bg-light-warning { background-color: #fffbeb; color: #f59e0b; }
    .bg-light-danger { background-color: #fef2f2; color: #ef4444; }
    
    .table-custom th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        color: #6b7280;
        border-bottom-width: 2px;
        background-color: #f8fafc;
        padding: 0.75rem 1rem;
    }
    
    .table-custom td {
        padding: 1rem;
        vertical-align: middle;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .card-title-custom {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0;
    }

    .badge-custom {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
</style>

{{-- Header Welcome --}}
<div class="mb-4">
    <h4 class="fw-bold mb-1 text-dark">Halo, {{ auth('officer')->user()->name ?? 'Petugas' }}</h4>
    <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    @php
        // Adjust column width based on whether HR or not
        $colClass = $isHrOfficer ? 'col-12 col-sm-6 col-xl-3' : 'col-12 col-md-4';
    @endphp

    {{-- Total Pegawai --}}
    <div class="{{ $colClass }}">
        <div class="dashboard-card card h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Total Pegawai</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalPegawai ?? 0 }}</h3>
                    </div>
                    <div class="stat-card-icon bg-light-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Hadir --}}
    <div class="{{ $colClass }}">
        <div class="dashboard-card card h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Hadir Hari Ini</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalHadir ?? 0 }}</h3>
                    </div>
                    <div class="stat-card-icon bg-light-success">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Lembur Aktif --}}
    <div class="{{ $colClass }}">
        <div class="dashboard-card card h-100 {{ count($overtimeList ?? []) > 0 ? 'border-warning' : '' }}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Sedang Lembur</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ count($overtimeList ?? []) }}</h3>
                    </div>
                    <div class="stat-card-icon bg-light-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isHrOfficer)
    {{-- Total Penggajian --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="dashboard-card card h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 small text-uppercase">Data Penggajian</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalPenggajian ?? 0 }}</h3>
                    </div>
                    <div class="stat-card-icon bg-light-danger">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row g-4 mb-4">
    {{-- Main Content Column --}}
    <div class="col-12 col-xl-8">
        {{-- Card Absensi Terakhir --}}
        <div class="dashboard-card card mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title-custom">Absensi Kedatangan Terakhir</h5>
                <a href="{{ route('officers.absensi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
            </div>
            <div class="card-body p-0">
                @if($recentAbsensi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-custom mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Departemen</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th class="text-center">Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAbsensi as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->pegawai?->nama_pegawai ?? '-' }}</td>
                                    <td>{{ $item->pegawai?->departemen?->nama_departemen ?? '-' }}</td>
                                    <td>
                                        @if(in_array(strtolower($item->status), ['izin', 'sakit']))
                                            <span class="badge bg-warning badge-custom">{{ ucfirst($item->status) }}</span>
                                        @else
                                            <span class="badge bg-success badge-custom">{{ $item->jam_masuk ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array(strtolower($item->status), ['izin', 'sakit']))
                                            <span class="text-muted small">-</span>
                                        @elseif($item->jam_pulang)
                                            <span class="badge bg-secondary badge-custom">{{ $item->jam_pulang }}</span>
                                        @else
                                            <span class="text-muted small">Belum Pulang</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->foto_masuk)
                                            <a href="{{ Storage::url($item->foto_masuk) }}" target="_blank" class="text-primary text-decoration-none">Lihat</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Belum ada data absensi hari ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Lembur dan Terlambat Side-by-side --}}
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="dashboard-card card h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title-custom text-dark">Data Lembur Berjalan</h5>
                    </div>
                    <div class="card-body p-3">
                        @if(count($overtimeList ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle">
                                    <tbody>
                                        @foreach($overtimeList as $ot)
                                        @php
                                            $menit = $ot->overtime_menit;
                                            $jam   = intdiv($menit, 60);
                                            $sisa  = $menit % 60;
                                            $label = ($jam > 0 ? $jam . 'j ' : '') . $sisa . 'm';
                                        @endphp
                                        <tr class="border-bottom">
                                            <td class="py-2">
                                                <div class="fw-semibold text-dark">{{ $ot->pegawai?->nama_pegawai ?? '-' }}</div>
                                                <div class="small text-muted">Jadwal Selesai: {{ \Carbon\Carbon::parse($ot->jam_masuk)->addHours(8)->format('H:i') ?? '-' }}</div>
                                            </td>
                                            <td class="text-end py-2">
                                                <span class="badge bg-warning text-dark badge-custom">+ {{ $label }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <p class="mb-0">Tidak ada pegawai yang sedang lembur.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="dashboard-card card h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title-custom text-dark">Pegawai Terlambat</h5>
                    </div>
                    <div class="card-body p-3">
                        @if(count($terlambatList) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle">
                                    <tbody>
                                        @foreach($terlambatList->take(5) as $ab)
                                        <tr class="border-bottom">
                                            <td class="py-2">
                                                <div class="fw-semibold text-dark">{{ $ab->pegawai?->nama_pegawai ?? '-' }}</div>
                                                <div class="small text-muted">Kedatangan: {{ $ab->jam_masuk }}</div>
                                            </td>
                                            <td class="text-end py-2">
                                                <span class="badge bg-danger badge-custom">{{ $ab->terlambat_menit }} mnt</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <p class="mb-0">Tidak ada data pegawai terlambat hari ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Column --}}
    <div class="col-12 col-xl-4">
        
        @if($isHrOfficer)
        {{-- Card Penggajian Terbaru --}}
        <div class="dashboard-card card mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title-custom">Penggajian Terbaru</h5>
            </div>
            <div class="card-body p-0">
                @if($recentPenggajian->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-custom mb-0 w-100">
                            <tbody>
                                @foreach($recentPenggajian as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->pegawai?->nama_pegawai ?? '-' }}</div>
                                        <div class="small text-muted">{{ $item->periode ?? '-' }}</div>
                                    </td>
                                    <td class="text-end align-middle fw-bold text-dark">
                                        Rp {{ number_format($item->gaji_bersih ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted small mb-0">Belum ada data penggajian.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Mini Stat Overview --}}
        <div class="dashboard-card card bg-light border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-dark text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Ringkasan Organisasi</h6>
                
                <div class="d-flex justify-content-between pb-2 mb-2 border-bottom">
                    <span class="text-muted">Pegawai Aktif</span>
                    <span class="fw-bold text-dark">{{ $totalPegawai ?? 0 }}</span>
                </div>
                
                <div class="d-flex justify-content-between pb-2 mb-2 border-bottom">
                    <span class="text-muted">Departemen</span>
                    <span class="fw-bold text-dark">{{ $totalDepartemen ?? 0 }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Jabatan</span>
                    <span class="fw-bold text-dark">{{ $totalJabatan ?? 0 }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
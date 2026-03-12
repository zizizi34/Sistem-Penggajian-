@extends('layouts.app')

@section('title', 'Beranda')
@section('description', 'Halaman Beranda')

@section('content')
<style>
    /* Premium Dashboard Utilities */
    .fw-extrabold { font-weight: 800 !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1) !important; color: #198754 !important; }
    .bg-light-primary { background-color: rgba(67, 94, 190, 0.1) !important; color: #435ebe !important; }
    .bg-light-info { background-color: rgba(13, 202, 240, 0.1) !important; color: #0dcaf0 !important; }
    
    .stats-card-inner {
        transition: all 0.3s ease;
        border-radius: 1.25rem;
    }
    .stats-card-inner:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }

    .stats-icon-modern {
        width: 56px !important;
        height: 56px !important;
        border-radius: 1rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .stats-icon-modern i {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.5rem !important;
        line-height: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .stats-icon-modern.blue { background: linear-gradient(135deg, #435ebe, #6a85e6); color: white; }
    .stats-icon-modern.green { background: linear-gradient(135deg, #198754, #28a745); color: white; }

    .card { border-radius: 1.25rem; border: none; }
    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6c757d;
        border-top: none;
    }
</style>

<section class="row">
  <div class="col-12">
    @if(isset($overtimeNotification))
    <div class="alert bg-light-warning border-0 shadow-sm d-flex align-items-center mb-4 p-4" style="border-radius: 1.25rem; border-left: 5px solid #ffc107 !important;">
      <div class="stats-icon-modern orange me-4 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(255, 193, 7, 0.2); color: #856404; width: 60px; height: 60px; border-radius: 1rem;">
        <i class="bi bi-clock-history fs-3"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-extrabold text-dark mb-1">Notifikasi Lembur Hari Ini!</h6>
        <p class="mb-0 text-muted small">
          Halo, Anda mendapatkan jatah lembur: <span class="badge bg-warning text-dark fw-bold px-3 rounded-pill ml-1">"{{ $overtimeNotification->keterangan ?: 'Anda diminta untuk bekerja lembur hari ini.' }}"</span>
        </p>
      </div>
      <div class="ms-auto d-none d-md-block text-end">
         <small class="text-muted d-block fw-bold mb-1">Batas Absen</small>
         <span class="badge bg-dark text-white px-3 py-2 rounded-pill fw-bold">21:00 WIB</span>
      </div>
    </div>
    @endif
  </div>

  <div class="col-12 col-lg-9">
    <div class="row g-4">
      <div class="col-6 col-md-6">
        <div class="card shadow-sm h-100 stats-card-inner bg-white">
          <a href="{{ route('students.payroll.index') }}" class="text-decoration-none">
            <div class="card-body p-4">
               <div class="stats-icon-modern blue d-flex align-items-center justify-content-center mb-3">
                  <i class="bi bi-wallet2 text-white fs-4" style="line-height: 0;"></i>
               </div>
               <h6 class="text-muted fw-bold mb-1">Slip Gaji Saya</h6>
               <h3 class="fw-extrabold text-dark mb-0">{{ $myPayrollCount ?? 0 }}</h3>
               <div class="mt-3 text-primary small fw-bold">
                   Lihat Detail <i class="bi bi-chevron-right ms-1" style="font-size: 0.7rem;"></i>
               </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-6 col-md-6">
        <div class="card shadow-sm h-100 stats-card-inner bg-white">
          <a href="{{ route('students.attendance.index') }}" class="text-decoration-none">
            <div class="card-body p-4">
               <div class="stats-icon-modern green d-flex align-items-center justify-content-center mb-3">
                  <i class="bi bi-person-check text-white fs-4" style="line-height: 0;"></i>
               </div>
               <h6 class="text-muted fw-bold mb-1">Absensi Saya</h6>
               <h3 class="fw-extrabold text-dark mb-0">{{ $myAttendanceCount ?? 0 }}</h3>
               <div class="mt-3 text-success small fw-bold">
                   Lihat Riwayat <i class="bi bi-chevron-right ms-1" style="font-size: 0.7rem;"></i>
               </div>
            </div>
          </a>
        </div>
      </div>
    </div>
    {{-- ... rest of table ... --}}
    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Slip Gaji Terbaru</h5>
            <a href="{{ route('students.payroll.index') }}" class="btn btn-sm btn-light rounded-pill px-3">Lihat Semua</a>
          </div>
          <div class="card-body p-4">
            @if($myPayrolls->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th class="ps-0">Periode</th>
                      <th>Gaji Pokok</th>
                      <th>Total Tunjangan</th>
                      <th>Total Potongan</th>
                      <th class="pe-0">Gaji Bersih</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($myPayrolls as $item)
                    <tr>
                      <td class="ps-0 fw-bold">{{ $item->periode ?? '-' }}</td>
                      <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                      <td class="text-success">+Rp {{ number_format($item->total_tunjangan ?? 0, 0, ',', '.') }}</td>
                      <td class="text-danger">-Rp {{ number_format($item->total_potongan ?? 0, 0, ',', '.') }}</td>
                      <td class="pe-0 fw-extrabold text-primary">Rp {{ number_format($item->gaji_bersih ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-inbox text-muted fs-3"></i>
                </div>
                <p class="text-muted fw-bold mb-0">Belum ada data slip gaji</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-3">
    {{-- Profile Mini Card --}}
    <div class="card shadow-sm mb-4 bg-white text-dark overflow-hidden border">
      <div class="card-body p-4 position-relative">
        <div class="position-relative" style="z-index: 2;">
            <p class="mb-1 small text-muted fw-bold">Halo, Selamat Datang</p>
            <h5 class="fw-extrabold mb-1 text-primary">{{ auth('student')->user()->pegawai?->nama_pegawai ?? 'User' }}</h5>
            <p class="small mb-0 text-muted text-truncate">{{ auth('student')->user()->email_user }}</p>
        </div>
        <i class="bi bi-person-circle position-absolute bottom-0 end-0 mb-n3 me-n2 opacity-10 text-primary" style="font-size: 6rem;"></i>
      </div>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Status Absensi</h6>
        @if(isset($todayAttendance))
            @php
                $statusColor = match($todayAttendance->status) {
                    'hadir', 'Lembur' => 'success',
                    'Pulang Cepat' => 'warning',
                    'terlambat' => 'danger',
                    default => 'secondary'
                };
            @endphp
            <span class="badge bg-light-{{ $statusColor }} text-{{ $statusColor }} rounded-pill small px-2">{{ $todayAttendance->status }}</span>
        @endif
      </div>
      <div class="card-body p-4">
        @if(isset($todayAttendance))
        <div class="mb-4">
           <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center">
                 <div class="bg-light-success p-2 rounded-2 me-3"><i class="bi bi-box-arrow-in-right"></i></div>
                 <span class="small fw-bold">Masuk</span>
              </div>
              <span class="fw-extrabold text-success">{{ $todayAttendance->jam_masuk }}</span>
           </div>
           <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                 <div class="bg-light-info p-2 rounded-2 me-3"><i class="bi bi-box-arrow-left"></i></div>
                 <span class="small fw-bold">Pulang</span>
              </div>
              <span class="fw-extrabold text-info">{{ $todayAttendance->jam_pulang ?? '--:--:--' }}</span>
           </div>
        </div>
        @else
        <div class="text-center bg-light p-3 rounded-3 mb-4">
            <p class="text-muted small fw-bold mb-0">Belum absen hari ini</p>
        </div>
        @endif

        <div class="d-grid">
            @if($todayAttendance && $todayAttendance->jam_pulang)
                <div class="alert bg-light-success border-0 p-2 px-3 small text-center mb-0 text-success fw-bold" style="border-radius: 0.75rem;">
                   <i class="bi bi-check-circle-fill me-1"></i> Absensi Selesai
                </div>
            @elseif($isClosed)
                <div class="alert bg-light-danger border-0 p-2 px-3 small text-center mb-0 text-danger fw-bold" style="border-radius: 0.75rem;">
                   <i class="bi bi-lock-fill me-1"></i> Absensi Ditutup
                </div>
            @else
                <a href="{{ route('students.attendance.index') }}" class="btn btn-primary fw-bold rounded-pill">
                    <i class="bi bi-fingerprint me-2"></i>Absen Sekarang
                </a>
            @endif
        </div>
      </div>
    </div>

    {{-- Quick Summary --}}
    <div class="card shadow-sm">
      <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0">Ringkasan Info</h6>
      </div>
      <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-light rounded-circle p-2 me-3"><i class="bi bi-file-earmark-text text-muted"></i></div>
            <div class="flex-grow-1">
                <span class="text-muted small d-block">Slip Gaji</span>
                <span class="fw-bold">{{ $myPayrollCount ?? 0 }} Dokumen</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="bg-light rounded-circle p-2 me-3"><i class="bi bi-calendar-check text-muted"></i></div>
            <div class="flex-grow-1">
                <span class="text-muted small d-block">Absensi</span>
                <span class="fw-bold">{{ $myAttendanceCount ?? 0 }} Record</span>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

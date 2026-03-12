@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('description', 'Informasi lengkap pegawai departemen.')

@section('content')

{{-- ALERTS --}}
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pegawai - Detail</h4>
    <a href="{{ route('officers.pegawai.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm" style="border-radius: 6px;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    {{-- KARTU KIRI: INFO PEGAWAI --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-lg-5">
                <div class="mb-5">
                    <p class="text-dark mb-2 fs-5">Nama</p>
                    <h3 class="text-dark fw-bold m-0" style="font-size: 1.7rem;">{{ $pegawai->nama_pegawai }}</h3>
                </div>
                <div>
                    <p class="text-dark mb-2 fs-5">Jabatan</p>
                    <h4 class="text-dark fw-bold m-0" style="font-size: 1.4rem;">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU KANAN: STATUS HARI INI & REKAP --}}
    <div class="col-12 col-lg-6 d-flex flex-column gap-3">
        {{-- KARTU ATAS: STATUS HARI INI --}}
        <div class="card border-0 shadow-sm rounded-3 flex-grow-1">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-dark mb-2 fs-6">Status Kehadiran Hari Ini</p>
                    @if($attendanceToday)
                        @if(in_array($attendanceToday->status, ['hadir', 'terlambat']))
                            <h4 class="fw-bold text-success mb-0">{{ ucfirst($attendanceToday->status) }}</h4>
                        @elseif($attendanceToday->status === 'izin')
                            <h4 class="fw-bold text-info mb-0">Izin</h4>
                        @elseif($attendanceToday->status === 'sakit')
                            <h4 class="fw-bold text-warning mb-0">Sakit</h4>
                        @else
                            <h4 class="fw-bold text-danger mb-0">{{ ucfirst($attendanceToday->status) }}</h4>
                        @endif
                        <small class="text-muted d-none mt-1">
                            Masuk: <strong>{{ $attendanceToday->jam_masuk ?? '-' }}</strong> | 
                            Pulang: <strong>{{ $attendanceToday->jam_pulang ?? '-' }}</strong>
                        </small>
                    @else
                        <h4 class="fw-bold text-danger mb-0">Tidak Hadir</h4>
                    @endif
                </div>

                {{-- BUTTON BERI IZIN --}}
                @if(!$attendanceToday || ($attendanceToday->status != 'approved' && $attendanceToday->status != 'izin' && !$attendanceToday->jam_masuk))
                <div>
                    <button type="button" class="btn btn-info text-white shadow-sm px-4 fw-semibold" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#modalBeriIzin">
                        <i class="bi bi-pencil-fill me-1"></i> Beri Izin
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- KARTU BAWAH: TOTAL KEHADIRAN / TOTAL IZIN / TOTAL ALPHA --}}
        <div class="card border-0 shadow-sm rounded-3 flex-grow-1">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="row g-0 w-100">
                    <div class="col-4 border-end">
                        <div class="p-2 py-0 text-start">
                            <span class="text-dark fs-6 d-block mb-3">Total Kehadiran</span>
                            <h3 class="fw-bold text-dark mb-0 fs-2 px-1">{{ $totalHadir }}</h3>
                        </div>
                    </div>
                    <div class="col-4 border-end ps-3">
                        <div class="p-2 py-0 text-start">
                            <span class="text-dark fs-6 d-block mb-3">Total Izin</span>
                            <h3 class="fw-bold text-dark mb-0 fs-2 px-1">{{ $totalIzin }}</h3>
                        </div>
                    </div>
                    <div class="col-4 ps-3">
                        <div class="p-2 py-0 text-start">
                            <span class="text-dark fs-6 d-block mb-3">Total Alpha</span>
                            <h3 class="fw-bold text-dark mb-0 fs-2 px-1">{{ $totalAlpha }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

{{-- MODAL BERI IZIN --}}
@if(!$attendanceToday || ($attendanceToday->status != 'approved' && $attendanceToday->status != 'izin' && !$attendanceToday->jam_masuk))
<div class="modal fade" id="modalBeriIzin" tabindex="-1" aria-labelledby="modalBeriIzinLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="modalBeriIzinLabel">Beri Izin untuk {{ $pegawai->nama_pegawai }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      @if($attendanceToday)
      <form action="{{ url('officer/absensi/' . $attendanceToday->id_absensi) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="izin">
      @else
      <form action="{{ route('officers.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_pegawai" value="{{ $pegawai->id_pegawai }}">
        <input type="hidden" name="tanggal_absensi" value="{{ $today }}">
        <input type="hidden" name="status" value="izin">
      @endif
        <div class="modal-body p-4">
          <p class="text-muted mb-3">Apakah Anda yakin ingin memberikan status izin untuk <strong>{{ $pegawai->nama_pegawai }}</strong> pada hari ini ({{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }})?</p>
          <div class="mb-3">
              <label class="form-label fw-semibold">Keterangan Izin</label>
              <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Sakit, keperluan keluarga..." required></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4 fw-semibold" style="border-radius:6px;" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info text-white px-4 fw-semibold shadow-sm" style="border-radius:6px;">Simpan Izin</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection

@push('script')
<script>
    setTimeout(function() {
        document.querySelectorAll('.auto-dismiss').forEach(function(el) {
            let bsAlert = new bootstrap.Alert(el);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush

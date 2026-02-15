@extends('layouts.app')

@section('title', 'Absensi')
@section('description', 'Halaman Absensi Harian')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header text-center">
                <h4>Absensi Hari Ini</h4>
                <p>{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(!$attendance)
                    <!-- Belum Absen Masuk -->
                    <div class="text-center mb-4">
                        <span class="badge bg-secondary p-2">Status: Belum Hadir</span>
                    </div>
                    <form action="{{ route('students.attendance.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="masuk">
                        <div class="form-group mb-3">
                            <label class="form-label">Foto Absen Masuk</label>
                            <input type="file" name="foto" class="form-control" accept="image/*;capture=camera" required>
                            <small class="text-muted">Ambil foto selfie di lokasi kerja.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Absen Masuk</button>
                        </div>
                    </form>
                @elseif($attendance->jam_masuk && !$attendance->jam_pulang)
                    <!-- Sudah Masuk, Belum Pulang -->
                    <div class="text-center mb-4">
                        <span class="badge bg-success p-2">Status: Hadir (Masuk: {{ $attendance->jam_masuk }})</span>
                    </div>
                    
                    <div class="alert alert-light border text-center">
                        <img src="{{ Storage::url($attendance->foto_masuk) }}" class="img-fluid rounded mb-2" style="max-height: 200px">
                        <p>Foto Masuk</p>
                    </div>

                    <form action="{{ route('students.attendance.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="pulang">
                        <div class="form-group mb-3">
                            <label class="form-label">Foto Absen Pulang</label>
                            <input type="file" name="foto" class="form-control" accept="image/*;capture=camera" required>
                             <small class="text-muted">Ambil foto selfie sebelum pulang.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">Absen Pulang</button>
                        </div>
                    </form>
                @else
                    <!-- Sudah Selesai -->
                    <div class="text-center">
                        <div class="mb-4">
                            <span class="badge bg-success p-2">Absensi Selesai</span>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-2">
                                        <small>Masuk</small>
                                        <h5>{{ $attendance->jam_masuk }}</h5>
                                        <img src="{{ Storage::url($attendance->foto_masuk) }}" class="img-fluid rounded" style="max-height: 100px">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-2">
                                        <small>Pulang</small>
                                        <h5>{{ $attendance->jam_pulang }}</h5>
                                        <img src="{{ Storage::url($attendance->foto_pulang) }}" class="img-fluid rounded" style="max-height: 100px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

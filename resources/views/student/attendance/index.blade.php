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

<div class="row justify-content-center mt-5">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Absensi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_absensi)->translatedFormat('l, d F Y') }}</td>
                                    <td>
                                        @if($item->jam_masuk)
                                            <span class="text-success"><i class="fas fa-sign-in-alt me-1"></i> {{ $item->jam_masuk }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jam_pulang)
                                            <span class="text-warning"><i class="fas fa-sign-out-alt me-1"></i> {{ $item->jam_pulang }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($item->status == 'terlambat')
                                            <span class="badge bg-warning">Terlambat</span>
                                        @elseif($item->status == 'izin')
                                            <span class="badge bg-info">Izin</span>
                                        @elseif($item->status == 'sakit')
                                            <span class="badge bg-primary">Sakit</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted border-0 py-3">Belum ada riwayat absensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    {{ $history->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

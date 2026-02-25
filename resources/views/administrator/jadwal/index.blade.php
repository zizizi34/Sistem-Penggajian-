@extends('layouts.app')

@section('title', 'Jadwal Kerja Departemen')
@section('description', 'Kelola Jam Masuk dan Pulang per Departemen')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Atur Jadwal</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('administrators.jadwal-kerja.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <select name="id_departemen" class="form-select" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hari</label>
                        <select name="hari" class="form-select" required>
                            <option value="Senin-Jumat">Senin - Jumat</option>
                            <option value="Senin-Sabtu">Senin - Sabtu</option>
                            <option value="Setiap Hari">Setiap Hari</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" name="jam_masuk" class="form-control" value="08:00" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Pulang</label>
                                <input type="time" name="jam_pulang" class="form-control" value="17:00" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toleransi Terlambat (Menit)</label>
                        <input type="number" name="toleransi_terlambat" class="form-control" value="0" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        @include('utilities.alert')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Jadwal Kerja</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Departemen</th>
                                <th>Hari</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Toleransi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $jadwal)
                            <tr>
                                <td>{{ $jadwal->departemen->nama_departemen }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td><span class="badge bg-success">{{ $jadwal->jam_masuk }}</span></td>
                                <td><span class="badge bg-danger">{{ $jadwal->jam_pulang }}</span></td>
                                <td>{{ $jadwal->toleransi_terlambat }} Menit</td>
                                <td>
                                    <form action="{{ route('administrators.jadwal-kerja.destroy', $jadwal->id_jadwal) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada jadwal yang diatur</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

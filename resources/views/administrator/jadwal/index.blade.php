@extends('layouts.app')

@section('title', 'Jadwal Kerja Departemen')
@section('description', 'Kelola Jam Masuk dan Pulang per Departemen')

@section('content')
<div class="row">
    <div class="col-12">
        @include('utilities.alert')
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Jadwal Kerja</h4>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
                </button>
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

{{-- Modal Tambah Jadwal --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('administrators.jadwal-kerja.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Atur Jadwal Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Departemen</label>
                        <select name="id_departemen" class="form-select" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Hari Kerja</label>
                        <select name="hari" class="form-select" required>
                            <option value="Senin-Jumat">Senin - Jumat</option>
                            <option value="Senin-Sabtu">Senin - Sabtu</option>
                            <option value="Setiap Hari">Setiap Hari</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" name="jam_masuk" class="form-control" value="08:00" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-4">
                                <label class="form-label">Jam Pulang</label>
                                <input type="time" name="jam_pulang" class="form-control" value="17:00" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toleransi Terlambat (Menit)</label>
                        <div class="input-group">
                            <input type="number" name="toleransi_terlambat" class="form-control" value="0" required min="0">
                            <span class="input-group-text">Menit</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-modal">Simpan Jadwal</button>
                </div>
            </div>
        </form>
    </div>
</div>


@push('script')
<script>
    $(document).ready(function() {
        @if($errors->any())
            $('#modalTambahJadwal').modal('show');
        @endif
    });
</script>
@endpush
@endsection

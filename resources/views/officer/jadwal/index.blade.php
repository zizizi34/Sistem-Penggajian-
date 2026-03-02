@extends('layouts.app')

@section('title', 'Jadwal Kerja')
@section('description', 'Kelola Jadwal Kerja Departemen Anda')

@section('content')

@include('utilities.alert')

<div class="row g-3">
    {{-- ===== FORM BUAT / UPDATE JADWAL ===== --}}
    <div class="col-12 col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="bi bi-calendar3 me-2"></i>Atur Jadwal Kerja
            </div>
            <div class="card-body p-4">
                {{-- Info Departemen (readonly) --}}
                <div class="alert alert-info py-2 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-building"></i>
                    <span>Departemen: <strong>{{ $departemen->nama_departemen }}</strong></span>
                </div>

                <form action="{{ route('officers.jadwal-kerja.store') }}" method="POST" id="form-jadwal">
                    @csrf

                    {{-- id_departemen tidak dikirim dari form karena controller yang set otomatis --}}

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hari Kerja</label>
                        <select name="hari" class="form-select" id="select-hari" required>
                            <option value="Senin-Jumat"   {{ isset($jadwals[0]) && $jadwals[0]->hari === 'Senin-Jumat'   ? 'selected' : '' }}>Senin – Jumat</option>
                            <option value="Senin-Sabtu"   {{ isset($jadwals[0]) && $jadwals[0]->hari === 'Senin-Sabtu'   ? 'selected' : '' }}>Senin – Sabtu</option>
                            <option value="Setiap Hari"   {{ isset($jadwals[0]) && $jadwals[0]->hari === 'Setiap Hari'   ? 'selected' : '' }}>Setiap Hari</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jam Masuk</label>
                            <input type="time" name="jam_masuk" id="input-jam-masuk"
                                class="form-control"
                                value="{{ $jadwals->first()->jam_masuk ?? '08:00' }}"
                                required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Jam Pulang</label>
                            <input type="time" name="jam_pulang" id="input-jam-pulang"
                                class="form-control"
                                value="{{ $jadwals->first()->jam_pulang ?? '17:00' }}"
                                required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Toleransi Terlambat <small class="text-muted">(menit)</small></label>
                        <input type="number" name="toleransi_terlambat" id="input-toleransi"
                            class="form-control"
                            value="{{ $jadwals->first()->toleransi_terlambat ?? 0 }}"
                            min="0" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="btn-simpan-jadwal">
                            <i class="bi bi-save me-1"></i>
                            {{ $jadwals->count() > 0 ? 'Perbarui Jadwal' : 'Simpan Jadwal' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== TABEL JADWAL AKTIF ===== --}}
    <div class="col-12 col-md-7 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2"></i>Jadwal Kerja Aktif</span>
                <span class="badge bg-secondary">{{ $departemen->nama_departemen }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Hari</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Toleransi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $jadwal)
                            <tr>
                                <td class="fw-semibold">{{ $jadwal->hari }}</td>
                                <td>
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ $jadwal->jam_masuk }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="bi bi-box-arrow-left me-1"></i>{{ $jadwal->jam_pulang }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>{{ $jadwal->toleransi_terlambat }} menit
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('officers.jadwal-kerja.destroy', $jadwal->id_jadwal) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" id="btn-hapus-jadwal-{{ $jadwal->id_jadwal }}">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 border-0">
                                    <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                                    Belum ada jadwal yang diatur untuk departemen ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Info box --}}
        <div class="alert alert-light border mt-3 small text-muted">
            <i class="bi bi-info-circle me-1 text-primary"></i>
            Jadwal kerja berlaku untuk semua pegawai di departemen <strong>{{ $departemen->nama_departemen }}</strong>.
            Jika jadwal sudah ada, menyimpan ulang akan <strong>memperbarui</strong> jadwal yang ada.
        </div>
    </div>
</div>

@endsection

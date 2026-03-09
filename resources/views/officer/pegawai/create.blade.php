@extends('layouts.app')

@section('title', 'Tambah Pegawai')
@section('description', 'Tambahkan pegawai baru ke departemen Anda')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Form Tambah Pegawai</h4>
                <a href="{{ route('officers.pegawai.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('officers.pegawai.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nik_pegawai" class="form-label">NIK Pegawai <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nik_pegawai" name="nik_pegawai" value="{{ old('nik_pegawai') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama_pegawai" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" value="{{ old('nama_pegawai') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_pegawai" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email_pegawai" name="email_pegawai" value="{{ old('email_pegawai') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Login <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Password minimal 6 karakter. Ini digunakan pegawai untuk login.</small>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 border-bottom pb-2">Informasi Pekerjaan</h5>

                    {{-- Departemen otomatis dari petugas, tidak bisa diubah --}}
                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        @php $officer = auth('officer')->user(); @endphp
                        <input type="text" class="form-control bg-light" value="{{ $officer->departemen->nama_departemen ?? '-' }}" readonly disabled>
                        <small class="text-muted"><i class="bi bi-lock"></i> Departemen otomatis mengikuti departemen Anda. Tidak dapat diubah.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            @if($jabatan->count() > 0)
                                <select class="form-select" id="id_jabatan" name="id_jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($jabatan as $j)
                                        <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan') == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hanya jabatan dari departemen <strong>{{ $officer->departemen->nama_departemen ?? '-' }}</strong> yang ditampilkan.</small>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Belum ada jabatan untuk departemen <strong>{{ $officer->departemen->nama_departemen ?? '-' }}</strong>.
                                    Hubungi Super Admin untuk menambahkan jabatan terlebih dahulu.
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_pegawai" class="form-label">Status Pegawai <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_pegawai" name="status_pegawai" required>
                                <option value="aktif" {{ old('status_pegawai', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="non-aktif" {{ old('status_pegawai') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tgl_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="{{ old('tgl_masuk', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gaji_pokok" class="form-label">Gaji Pokok (Kustom jika diperlukan)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok') }}" min="0" step="1" placeholder="Opsional">
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 border-bottom pb-2">Informasi Pajak & Bank (Opsional)</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_ptkp_status" class="form-label">Status PTKP</label>
                            <select class="form-select" id="id_ptkp_status" name="id_ptkp_status">
                                <option value="">Pilih Status PTKP</option>
                                @foreach($ptkpStatus as $ptkp)
                                    <option value="{{ $ptkp->id_ptkp_status }}" {{ old('id_ptkp_status') == $ptkp->id_ptkp_status ? 'selected' : '' }}>
                                        {{ $ptkp->kode_ptkp_status }} ({{ $ptkp->deskripsi }}) - Rp {{ number_format($ptkp->nominal, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control" id="npwp" name="npwp" value="{{ old('npwp') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_pegawai" class="form-label">Nama Bank</label>
                            <input type="text" class="form-control" id="bank_pegawai" name="bank_pegawai" value="{{ old('bank_pegawai') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no_rekening" class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" id="no_rekening" name="no_rekening" value="{{ old('no_rekening') }}">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pegawai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

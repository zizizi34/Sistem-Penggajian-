<!-- Modal Tambah Pegawai -->
<div class="modal fade" id="modalTambahPegawai" tabindex="-1" aria-labelledby="modalTambahPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('officers.pegawai.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="modalTambahPegawaiLabel"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pegawai Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_nik_pegawai" class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_nik_pegawai" name="nik_pegawai" value="{{ old('nik_nik_pegawai') }}" required placeholder="Contoh: 12345">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_nama_pegawai" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_nama_pegawai" name="nama_pegawai" value="{{ old('nama_pegawai') }}" required placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="create_alamat" name="alamat" rows="2" required placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_no_hp" class="form-label fw-semibold">No. HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_no_hp" name="no_hp" value="{{ old('no_hp') }}" required placeholder="Contoh: 08123456789">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_email_pegawai" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="create_email_pegawai" name="email_pegawai" value="{{ old('email_pegawai') }}" required placeholder="email@contoh.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_password" class="form-label fw-semibold">Password Akun <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create_password" name="password" required placeholder="Minimal 6 karakter">
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2 border-bottom pb-1 text-primary"><i class="bi bi-briefcase me-1"></i> Informasi Pekerjaan</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_id_jabatan" class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_id_jabatan" name="id_jabatan" required>
                                <option value="">Pilih Jabatan</option>
                                @foreach($jabatan as $j)
                                    <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan') == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_status_pegawai" class="form-label fw-semibold">Status Pegawai <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_status_pegawai" name="status_pegawai" required>
                                <option value="aktif" {{ old('status_pegawai', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="non-aktif" {{ old('status_pegawai') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_tgl_masuk" class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tgl_masuk" name="tgl_masuk" value="{{ old('tgl_masuk', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_gaji_pokok" class="form-label fw-semibold">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="create_gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok') }}" min="0" placeholder="Biarkan kosong untuk gaji min jabatan">
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2 border-bottom pb-1 text-primary"><i class="bi bi-bank me-1"></i> Pajak & Rekening (Opsional)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_id_ptkp_status" class="form-label fw-semibold text-truncate d-block">Status PTKP</label>
                            <select class="form-select" id="create_id_ptkp_status" name="id_ptkp_status">
                                <option value="">Pilih Status PTKP</option>
                                @foreach($ptkpStatus as $ptkp)
                                    <option value="{{ $ptkp->id_ptkp_status }}" {{ old('id_ptkp_status') == $ptkp->id_ptkp_status ? 'selected' : '' }}>
                                        {{ $ptkp->kode_ptkp_status }} ({{ $ptkp->deskripsi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_npwp" class="form-label fw-semibold">NPWP</label>
                            <input type="text" class="form-control" id="create_npwp" name="npwp" value="{{ old('npwp') }}" placeholder="Masukkan NPWP">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_bank_pegawai" class="form-label fw-semibold">Nama Bank</label>
                            <input type="text" class="form-control" id="create_bank_pegawai" name="bank_pegawai" value="{{ old('bank_pegawai') }}" placeholder="Contoh: BCA, Mandiri">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_no_rekening" class="form-label fw-semibold">No. Rekening</label>
                            <input type="text" class="form-control" id="create_no_rekening" name="no_rekening" value="{{ old('no_rekening') }}" placeholder="Masukkan no rekening">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Pegawai
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

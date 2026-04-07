<!-- Modal Tambah Pegawai (Admin) -->
<div class="modal fade" id="modalTambahPegawai" tabindex="-1" aria-labelledby="modalTambahPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('administrators.pegawai.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahPegawaiLabel"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pegawai (Admin)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="create_nik_pegawai" class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_nik_pegawai" name="nik_pegawai" required placeholder="Masukkan NIK">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="create_nama_pegawai" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_nama_pegawai" name="nama_pegawai" required placeholder="Masukkan Nama Lengkap">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="create_id_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_id_departemen" name="id_departemen" required>
                                <option value="">Pilih Departemen</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="create_id_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_id_jabatan" name="id_jabatan" required>
                                <option value="">Pilih Departemen Terlebih Dahulu</option>
                                @foreach($jabatans as $jab)
                                    <option value="{{ $jab->id_jabatan }}" class="jabatan-option" data-departemen="{{ $jab->id_departemen }}" style="display:none;">{{ $jab->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="create_jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="create_tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="create_email_pegawai" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="create_email_pegawai" name="email_pegawai" required placeholder="email@contoh.com">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="create_password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create_password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="create_no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_no_hp" name="no_hp" required placeholder="08xxxxxx">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="create_tgl_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_tgl_masuk" name="tgl_masuk" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="create_gaji_pokok" class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="create_gaji_pokok" name="gaji_pokok" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="create_status_pegawai" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_status_pegawai" name="status_pegawai" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="create_alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="create_alamat" name="alamat" rows="2" required placeholder="Masukkan Alamat Lengkap"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_id_ptkp_status" class="form-label">Status PTKP</label>
                            <select class="form-select" id="create_id_ptkp_status" name="id_ptkp_status">
                                <option value="">Pilih Status PTKP</option>
                                @foreach($ptkpStatus as $ptkp)
                                    <option value="{{ $ptkp->id_ptkp_status }}">{{ $ptkp->kode_ptkp_status }} ({{ $ptkp->deskripsi }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-modal">Simpan Pegawai</button>
                </div>
            </div>
        </form>
    </div>
</div>


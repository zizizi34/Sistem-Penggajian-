<!-- Modal Edit Pegawai -->
<div class="modal fade" id="modalEditPegawai" tabindex="-1" aria-labelledby="modalEditPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditPegawai" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning py-3">
                    <h5 class="modal-title fw-bold" id="modalEditPegawaiLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nik_pegawai" class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nik_pegawai" name="nik_pegawai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama_pegawai" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_pegawai" name="nama_pegawai" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_no_hp" class="form-label fw-semibold">No. HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_no_hp" name="no_hp" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email_pegawai" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email_pegawai" name="email_pegawai" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label fw-semibold">Ganti Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                            <small class="text-muted text-xs">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2 border-bottom pb-1 text-primary"><i class="bi bi-briefcase me-1"></i> Informasi Pekerjaan</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_id_jabatan" class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_id_jabatan" name="id_jabatan" required>
                                @foreach($jabatan as $j)
                                    <option value="{{ $j->id_jabatan }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status_pegawai" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status_pegawai" name="status_pegawai" required>
                                <option value="aktif">Aktif</option>
                                <option value="non-aktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_tgl_masuk" class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tgl_masuk" name="tgl_masuk" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_gaji_pokok" class="form-label fw-semibold">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_gaji_pokok" name="gaji_pokok" min="0">
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2 border-bottom pb-1 text-primary"><i class="bi bi-bank me-1"></i> Pajak & Rekening</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_id_ptkp_status" class="form-label fw-semibold d-block text-truncate">Status PTKP</label>
                            <select class="form-select" id="edit_id_ptkp_status" name="id_ptkp_status">
                                <option value="">Pilih Status PTKP</option>
                                @foreach($ptkpStatus as $ptkp)
                                    <option value="{{ $ptkp->id_ptkp_status }}">
                                        {{ $ptkp->kode_ptkp_status }} ({{ $ptkp->deskripsi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_npwp" class="form-label fw-semibold">NPWP</label>
                            <input type="text" class="form-control" id="edit_npwp" name="npwp">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_bank_pegawai" class="form-label fw-semibold">Nama Bank</label>
                            <input type="text" class="form-control" id="edit_bank_pegawai" name="bank_pegawai">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_no_rekening" class="form-label fw-semibold">No. Rekening</label>
                            <input type="text" class="form-control" id="edit_no_rekening" name="no_rekening">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Update Pegawai
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

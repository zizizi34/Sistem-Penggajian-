<!-- Modal Edit Pegawai (Admin) -->
<div class="modal fade" id="modalEditPegawai" tabindex="-1" aria-labelledby="modalEditPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditPegawai" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditPegawaiLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Pegawai (Admin)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="edit_nik_pegawai" class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nik_pegawai" name="nik_pegawai" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="edit_nama_pegawai" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_pegawai" name="nama_pegawai" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="edit_id_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_id_departemen" name="id_departemen" required>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="edit_id_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_id_jabatan" name="id_jabatan" required>
                                @foreach($jabatans as $jab)
                                    <option value="{{ $jab->id_jabatan }}">{{ $jab->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="edit_tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="edit_email_pegawai" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email_pegawai" name="email_pegawai" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="edit_password" class="form-label">Password (Baru)</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Biarkan kosong jika tetap">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="edit_no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_no_hp" name="no_hp" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="edit_tgl_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tgl_masuk" name="tgl_masuk" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="edit_gaji_pokok" class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_gaji_pokok" name="gaji_pokok" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="edit_status_pegawai" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status_pegawai" name="status_pegawai" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="edit_alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_id_ptkp_status" class="form-label">Status PTKP</label>
                            <select class="form-select" id="edit_id_ptkp_status" name="id_ptkp_status">
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
                    <button type="submit" class="btn btn-submit-modal">Update Pegawai</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editOfficerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Data Petugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="#" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-4">
            <label for="name_edit" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name_edit" class="form-control" placeholder="Masukkan nama petugas...">
          </div>

          <div class="mb-4">
            <label for="id_departemen_edit" class="form-label">Departemen <span class="text-secondary">(Opsional)</span></label>
            <select name="id_departemen" id="id_departemen_edit" class="form-select">
              <option value="">Pilih Departemen...</option>
              @foreach($departemens as $dept)
              <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-4">
            <label for="email_edit" class="form-label">Alamat Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" id="email_edit" class="form-control" placeholder="email@contoh.com">
            </div>
          </div>

          <div class="mb-4">
            <label for="phone_number_edit" class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input type="text" name="phone_number" id="phone_number_edit" class="form-control" placeholder="08xxxxxxxxxx">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-4">
              <label for="password_edit" class="form-label">Password <span class="text-secondary">(Opsional)</span></label>
              <input type="password" name="password" id="password_edit" class="form-control" placeholder="••••••••">
              <small class="text-muted">Biarkan kosong jika tidak diubah</small>
            </div>
            <div class="col-md-6 mb-4">
              <label for="password_confirmation_edit" class="form-label">Konfirmasi</label>
              <input type="password" name="password_confirmation" id="password_confirmation_edit" class="form-control" placeholder="••••••••">
            </div>
          </div>

          <div class="modal-footer px-0 pb-0">
            <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-submit-modal">Update Petugas</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


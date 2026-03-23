<div class="modal fade" id="createOfficerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Tambah Petugas Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('administrators.officers.store') }}" method="POST">
          @csrf
          <div class="mb-4">
            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama petugas..." value="{{ old('name') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="id_departemen" class="form-label">Departemen <span class="text-secondary">(Opsional)</span></label>
            <select name="id_departemen" id="id_departemen" class="form-select @error('id_departemen') is-invalid @enderror">
              <option value="">Pilih Departemen...</option>
              @foreach($departemens as $dept)
              <option value="{{ $dept->id_departemen }}" {{ old('id_departemen') == $dept->id_departemen ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
              @endforeach
            </select>
            @error('id_departemen')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com" value="{{ old('email') }}" required>
            </div>
            @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="phone_number" class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input type="number" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}" required>
            </div>
            @error('phone_number')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-4">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
              @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label for="password_confirmation" class="form-label">Konfirmasi <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>
          </div>

          <div class="modal-footer px-0 pb-0">
            <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-submit-modal">Simpan Petugas</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="createAdministratorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-shield-lock-fill me-2"></i>Tambah Administrator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('administrators.users.store') }}" method="POST">
          @csrf
          <div class="mb-4">
            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama administrator..." @if($errors->hasBag('store')) value="{{ old('name') }}" @endif required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com" @if($errors->hasBag('store')) value="{{ old('email') }}" @endif required>
            </div>
            @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="phone_number" class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input type="number" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" placeholder="08xxxxxxxxxx" @if($errors->hasBag('store')) value="{{ old('phone_number') }}" @endif required>
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
            <button type="submit" class="btn btn-submit-modal">Simpan Admin</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
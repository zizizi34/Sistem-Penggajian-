@extends('layouts.app')

@section('title', 'Data Jabatan')
@section('description', 'Kelola Data Jabatan')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Jabatan</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addJabatanModal">
          <i class="bi bi-plus"></i> Tambah Jabatan
        </button>
      </div>
      <div class="card-body">
        @if($jabatan->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama Jabatan</th>
                  <th>Departemen</th>
                  <th>Min Gaji</th>
                  <th>Max Gaji</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($jabatan as $i => $item)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $item->nama_jabatan ?? '-' }}</td>
                  <td>
                    @if($item->departemen)
                      <span class="badge bg-primary">{{ $item->departemen->nama_departemen }}</span>
                    @else
                      <span class="badge bg-secondary">Belum diatur</span>
                    @endif
                  </td>
                  <td>Rp {{ number_format($item->min_gaji ?? 0, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($item->max_gaji ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-jabatan"
                      data-id="{{ $item->id_jabatan }}"
                      data-nama="{{ $item->nama_jabatan }}"
                      data-id_departemen="{{ $item->id_departemen }}"
                      data-min_gaji="{{ $item->min_gaji }}"
                      data-max_gaji="{{ $item->max_gaji }}"
                      data-url="{{ route('administrators.jabatan.update', $item->id_jabatan) }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger"
                      data-delete-action="{{ route('administrators.jabatan.destroy', $item->id_jabatan) }}"
                      data-delete-item="{{ $item->nama_jabatan }}"
                      data-delete-category="jabatan">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted py-3">Belum ada data jabatan</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')

<!-- Modal Tambah -->
<div class="modal fade" id="addJabatanModal" tabindex="-1" aria-labelledby="addJabatanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addJabatanModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Jabatan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('administrators.jabatan.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-4">
              <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
              <input type="text" name="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror" placeholder="Contoh: Manager Operasional" value="{{ old('nama_jabatan') }}" required>
              @error('nama_jabatan')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Departemen <span class="text-danger">*</span></label>
              <select name="id_departemen" class="form-select @error('id_departemen') is-invalid @enderror" required>
                <option value="">-- Pilih Departemen --</option>
                @foreach($departemen as $d)
                  <option value="{{ $d->id_departemen }}" {{ old('id_departemen') == $d->id_departemen ? 'selected' : '' }}>{{ $d->nama_departemen }}</option>
                @endforeach
              </select>
              @error('id_departemen')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Min Gaji <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="min_gaji" class="form-control @error('min_gaji') is-invalid @enderror" value="{{ old('min_gaji', 0) }}" required min="0">
              </div>
              @error('min_gaji')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Max Gaji <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="max_gaji" class="form-control @error('max_gaji') is-invalid @enderror" value="{{ old('max_gaji', 0) }}" required min="0">
              </div>
              @error('max_gaji')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Simpan Jabatan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editJabatanModal" tabindex="-1" aria-labelledby="editJabatanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editJabatanModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Jabatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editJabatanForm" action="" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-4">
              <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
              <input type="text" name="nama_jabatan" id="edit_nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror" required>
              @error('nama_jabatan')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Departemen <span class="text-danger">*</span></label>
              <select name="id_departemen" id="edit_id_departemen" class="form-select @error('id_departemen') is-invalid @enderror" required>
                @foreach($departemen as $d)
                  <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                @endforeach
              </select>
              @error('id_departemen')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Min Gaji <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="min_gaji" id="edit_min_gaji" class="form-control @error('min_gaji') is-invalid @enderror" required min="0">
              </div>
              @error('min_gaji')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-4">
              <label class="form-label">Max Gaji <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="max_gaji" id="edit_max_gaji" class="form-control @error('max_gaji') is-invalid @enderror" required min="0">
              </div>
              @error('max_gaji')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Update Jabatan</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('script')
<script>
  $(document).ready(function() {
    $('.btn-edit-jabatan').click(function() {
      const id = $(this).data('id');
      const nama = $(this).data('nama');
      const dept = $(this).data('id_departemen');
      const min = $(this).data('min_gaji');
      const max = $(this).data('max_gaji');
      const url = $(this).data('url');
      
      $('#editJabatanForm').attr('action', url);
      $('#edit_nama_jabatan').val(nama);
      $('#edit_id_departemen').val(dept);
      $('#edit_min_gaji').val(min);
      $('#edit_max_gaji').val(max);
      
      $('#editJabatanModal').modal('show');
    });

    @if ($errors->any())
        @if(old('_method') == 'PUT')
            $('#editJabatanModal').modal('show');
        @else
            $('#addJabatanModal').modal('show');
        @endif
    @endif
  });
</script>
@endpush
@endsection

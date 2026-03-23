@extends('layouts.app')

@section('title', 'Data Departemen')
@section('description', 'Kelola Data Departemen')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Departemen</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDepartemenModal">
          <i class="bi bi-plus"></i> Tambah
        </button>
      </div>
      <div class="card-body">
        @if($departemen->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Departemen</th>
                  <th>Petugas</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($departemen as $item)
                <tr>
                  <td>{{ $item->nama_departemen ?? '-' }}</td>
                  <td>{{ $item->officers_count }} Petugas</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-departemen" 
                      data-id="{{ $item->id_departemen }}"
                      data-nama="{{ $item->nama_departemen }}"
                      data-url="{{ route('administrators.departemen.update', $item->id_departemen) }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.departemen.destroy', $item->id_departemen) }}"
                      data-delete-item="{{ $item->nama_departemen }}"
                      data-delete-category="departemen">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data departemen</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')

<!-- Modal Tambah -->
<div class="modal fade" id="addDepartemenModal" tabindex="-1" aria-labelledby="addDepartemenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDepartemenModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Departemen Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('administrators.departemen.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
            <input type="text" name="nama_departemen" class="form-control @error('nama_departemen') is-invalid @enderror" placeholder="Contoh: Teknologi Informasi" value="{{ old('nama_departemen') }}" required>
            @error('nama_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Simpan Departemen</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDepartemenModal" tabindex="-1" aria-labelledby="editDepartemenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDepartemenModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Departemen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editDepartemenForm" action="" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
            <input type="text" name="nama_departemen" id="edit_nama_departemen" class="form-control @error('nama_departemen') is-invalid @enderror" value="{{ old('nama_departemen') }}" required>
            @error('nama_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Update Departemen</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('script')
<script>
  $(document).ready(function() {
    $('.btn-edit-departemen').click(function() {
      const id = $(this).data('id');
      const nama = $(this).data('nama');
      const url = $(this).data('url');
      
      $('#editDepartemenForm').attr('action', url);
      $('#edit_nama_departemen').val(nama);
      
      $('#editDepartemenModal').modal('show');
    });

    // Re-open modal if there's an error
    @if ($errors->any())
        @if(old('_method') == 'PUT')
            $('#editDepartemenModal').modal('show');
        @else
            $('#addDepartemenModal').modal('show');
        @endif
    @endif
  });
</script>
@endpush
@endsection

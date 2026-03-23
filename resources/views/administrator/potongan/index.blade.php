@extends('layouts.app')

@section('title', 'Data Potongan')
@section('description', 'Kelola Data Potongan')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Potongan</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPotonganModal">
          <i class="bi bi-plus"></i> Tambah
        </button>
      </div>
      <div class="card-body">
        @if($potongan->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Potongan</th>
                  <th>Nominal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($potongan as $item)
                <tr>
                  <td>{{ $item->nama_potongan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-potongan"
                      data-id="{{ $item->id_potongan }}"
                      data-nama="{{ $item->nama_potongan }}"
                      data-nominal="{{ $item->nominal }}"
                      data-url="{{ route('administrators.potongan.update', $item->id_potongan) }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.potongan.destroy', $item->id_potongan) }}"
                      data-delete-item="{{ $item->nama_potongan }}"
                      data-delete-category="potongan">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data potongan</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')

<!-- Modal Tambah -->
<div class="modal fade" id="addPotonganModal" tabindex="-1" aria-labelledby="addPotonganModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPotonganModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Potongan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('administrators.potongan.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Potongan <span class="text-danger">*</span></label>
            <input type="text" name="nama_potongan" class="form-control @error('nama_potongan') is-invalid @enderror" value="{{ old('nama_potongan') }}" required placeholder="Contoh: Potongan BPJS">
            @error('nama_potongan')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nominal <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal') }}" required min="0" placeholder="0">
            </div>
            @error('nominal')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Simpan Potongan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editPotonganModal" tabindex="-1" aria-labelledby="editPotonganModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPotonganModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Potongan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPotonganForm" action="" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Potongan <span class="text-danger">*</span></label>
            <input type="text" name="nama_potongan" id="edit_nama_potongan" class="form-control @error('nama_potongan') is-invalid @enderror" required>
            @error('nama_potongan')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nominal <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" name="nominal" id="edit_nominal" class="form-control @error('nominal') is-invalid @enderror" required min="0">
            </div>
            @error('nominal')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Update Potongan</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('script')
<script>
  $(document).ready(function() {
    $('.btn-edit-potongan').click(function() {
      const id = $(this).data('id');
      const nama = $(this).data('nama');
      const nominal = $(this).data('nominal');
      const url = $(this).data('url');
      
      $('#editPotonganForm').attr('action', url);
      $('#edit_nama_potongan').val(nama);
      $('#edit_nominal').val(nominal);
      
      $('#editPotonganModal').modal('show');
    });

    @if ($errors->any())
        @if(old('_method') == 'PUT')
            $('#editPotonganModal').modal('show');
        @else
            $('#addPotonganModal').modal('show');
        @endif
    @endif
  });
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Data Tunjangan')
@section('description', 'Kelola Data Tunjangan')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Tunjangan</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTunjanganModal">
          <i class="bi bi-plus"></i> Tambah
        </button>
      </div>
      <div class="card-body">
        @if($tunjangan->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Tunjangan</th>
                  <th>Nominal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tunjangan as $item)
                <tr>
                  <td>{{ $item->nama_tunjangan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-tunjangan"
                      data-id="{{ $item->id_tunjangan }}"
                      data-nama="{{ $item->nama_tunjangan }}"
                      data-nominal="{{ $item->nominal }}"
                      data-url="{{ route('administrators.tunjangan.update', $item->id_tunjangan) }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.tunjangan.destroy', $item->id_tunjangan) }}"
                      data-delete-item="{{ $item->nama_tunjangan }}"
                      data-delete-category="tunjangan">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data tunjangan</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')

<!-- Modal Tambah -->
<div class="modal fade" id="addTunjanganModal" tabindex="-1" aria-labelledby="addTunjanganModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTunjanganModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Tunjangan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('administrators.tunjangan.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Tunjangan <span class="text-danger">*</span></label>
            <input type="text" name="nama_tunjangan" class="form-control @error('nama_tunjangan') is-invalid @enderror" value="{{ old('nama_tunjangan') }}" required placeholder="Contoh: Tunjangan Makan">
            @error('nama_tunjangan')
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
          <button type="submit" class="btn btn-submit-modal">Simpan Tunjangan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editTunjanganModal" tabindex="-1" aria-labelledby="editTunjanganModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTunjanganModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Tunjangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editTunjanganForm" action="" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Tunjangan <span class="text-danger">*</span></label>
            <input type="text" name="nama_tunjangan" id="edit_nama_tunjangan" class="form-control @error('nama_tunjangan') is-invalid @enderror" required>
            @error('nama_tunjangan')
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
          <button type="submit" class="btn btn-submit-modal">Update Tunjangan</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('script')
<script>
  $(document).ready(function() {
    $('.btn-edit-tunjangan').click(function() {
      const id = $(this).data('id');
      const nama = $(this).data('nama');
      const nominal = $(this).data('nominal');
      const url = $(this).data('url');
      
      $('#editTunjanganForm').attr('action', url);
      $('#edit_nama_tunjangan').val(nama);
      $('#edit_nominal').val(nominal);
      
      $('#editTunjanganModal').modal('show');
    });

    @if ($errors->any())
        @if(old('_method') == 'PUT')
            $('#editTunjanganModal').modal('show');
        @else
            $('#addTunjanganModal').modal('show');
        @endif
    @endif
  });
</script>
@endpush
@endsection

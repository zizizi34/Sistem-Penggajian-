@extends('layouts.app')

@section('title', 'Data Status PTKP')
@section('description', 'Kelola Data Status PTKP')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Status PTKP</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPtkpModal">
          <i class="bi bi-plus"></i> Tambah
        </button>
      </div>
      <div class="card-body">
        @if($ptkpStatus->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Kode</th>
                  <th>Deskripsi</th>
                  <th>Nominal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ptkpStatus as $item)
                <tr>
                  <td>{{ $item->kode_ptkp_status ?? '-' }}</td>
                  <td>{{ $item->deskripsi ?? '-' }}</td>
                  <td>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <a href="{{ route('administrators.ptkp-status.show', $item->id_ptkp_status) }}" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-warning btn-edit-ptkp"
                      data-id="{{ $item->id_ptkp_status }}"
                      data-kode="{{ $item->kode_ptkp_status }}"
                      data-deskripsi="{{ $item->deskripsi }}"
                      data-nominal="{{ $item->nominal }}"
                      data-url="{{ route('administrators.ptkp-status.update', $item->id_ptkp_status) }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.ptkp-status.destroy', $item->id_ptkp_status) }}"
                      data-delete-item="{{ $item->deskripsi ?? $item->kode_ptkp_status }}"
                      data-delete-category="status PTKP">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data status PTKP</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')

<!-- Modal Tambah -->
<div class="modal fade" id="addPtkpModal" tabindex="-1" aria-labelledby="addPtkpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPtkpModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Status PTKP Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('administrators.ptkp-status.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kode PTKP</label>
            <input type="text" name="kode_ptkp_status" class="form-control @error('kode_ptkp_status') is-invalid @enderror" value="{{ old('kode_ptkp_status') }}" placeholder="Contoh: K/0">
            @error('kode_ptkp_status')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" value="{{ old('deskripsi') }}" placeholder="Contoh: Kawin tanpa tanggungan">
            @error('deskripsi')
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
          <button type="submit" class="btn btn-submit-modal">Simpan Status PTKP</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editPtkpModal" tabindex="-1" aria-labelledby="editPtkpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPtkpModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Data Status PTKP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPtkpForm" action="" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kode PTKP</label>
            <input type="text" name="kode_ptkp_status" id="edit_kode_ptkp" class="form-control @error('kode_ptkp_status') is-invalid @enderror">
            @error('kode_ptkp_status')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="deskripsi" id="edit_deskripsi" class="form-control @error('deskripsi') is-invalid @enderror">
            @error('deskripsi')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Nominal <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" name="nominal" id="edit_nominal_ptkp" class="form-control @error('nominal') is-invalid @enderror" required min="0">
            </div>
            @error('nominal')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-submit-modal">Update Status PTKP</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('script')
<script>
  $(document).ready(function() {
    $('.btn-edit-ptkp').click(function() {
      const id = $(this).data('id');
      const kode = $(this).data('kode');
      const deskripsi = $(this).data('deskripsi');
      const nominal = $(this).data('nominal');
      const url = $(this).data('url');
      
      $('#editPtkpForm').attr('action', url);
      $('#edit_kode_ptkp').val(kode);
      $('#edit_deskripsi').val(deskripsi);
      $('#edit_nominal_ptkp').val(nominal);
      
      $('#editPtkpModal').modal('show');
    });

    @if ($errors->any())
        @if(old('_method') == 'PUT')
            $('#editPtkpModal').modal('show');
        @else
            $('#addPtkpModal').modal('show');
        @endif
    @endif
  });
</script>
@endpush
@endsection

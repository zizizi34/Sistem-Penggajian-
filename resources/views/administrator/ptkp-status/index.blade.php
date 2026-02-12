@extends('layouts.app')

@section('title', 'Data Status PTKP')
@section('description', 'Kelola Data Status PTKP')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Status PTKP</h4>
        <a href="{{ route('administrators.ptkp-status.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
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
                    <a href="{{ route('administrators.ptkp-status.edit', $item->id_ptkp_status) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-ptkp" data-action="{{ route('administrators.ptkp-status.destroy', $item->id_ptkp_status) }}" data-name="{{ $item->deskripsi ?? $item->kode_ptkp_status }}">
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
  <!-- Delete confirmation modal -->
  <div class="modal fade" id="confirmDeleteModalPtkp" tabindex="-1" aria-labelledby="confirmDeleteLabelPtkp" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabelPtkp">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Yakin ingin menghapus status PTKP <strong id="deleteItemNamePtkp"></strong> ?</p>
        </div>
        <div class="modal-footer">
          <form id="deleteFormPtkp" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('script')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.btn-delete-ptkp').forEach(function(btn) {
        btn.addEventListener('click', function () {
          var action = this.getAttribute('data-action');
          var name = this.getAttribute('data-name') || '';
          var deleteForm = document.getElementById('deleteFormPtkp');
          var deleteItemName = document.getElementById('deleteItemNamePtkp');
          deleteForm.action = action;
          deleteItemName.textContent = name;
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalEl = document.getElementById('confirmDeleteModalPtkp');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
          } else {
            if (confirm('Yakin ingin menghapus status PTKP "' + name + '" ?')) {
              deleteForm.submit();
            }
          }
        });
      });
    });
  </script>
  @endpush
@endsection

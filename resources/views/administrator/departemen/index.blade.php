@extends('layouts.app')

@section('title', 'Data Departemen')
@section('description', 'Kelola Data Departemen')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Departemen</h4>
        <a href="{{ route('administrators.departemen.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
      </div>
      <div class="card-body">
        @if($departemen->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Departemen</th>
                  <th>Manager</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($departemen as $item)
                <tr>
                  <td>{{ $item->nama_departemen ?? '-' }}</td>
                  <td>{{ $item->manager_departemen ?? '-' }}</td>
                  <td>
                    <a href="{{ route('administrators.departemen.edit', $item->id_departemen) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-departemen" data-action="{{ route('administrators.departemen.destroy', $item->id_departemen) }}" data-name="{{ $item->nama_departemen }}">
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
<!-- Delete confirmation modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus departemen <strong id="deleteItemName"></strong> ?</p>
      </div>
      <div class="modal-footer">
        <form id="deleteForm" method="POST" action="">
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
    var deleteButtons = document.querySelectorAll('.btn-delete-departemen');
    deleteButtons.forEach(function(btn) {
      btn.addEventListener('click', function () {
        var action = this.getAttribute('data-action');
        var name = this.getAttribute('data-name') || '';
        var deleteForm = document.getElementById('deleteForm');
        var deleteItemName = document.getElementById('deleteItemName');
        deleteForm.action = action;
        deleteItemName.textContent = name;
        // Try to show Bootstrap modal if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modalEl = document.getElementById('confirmDeleteModal');
          var modal = new bootstrap.Modal(modalEl);
          modal.show();
        } else {
          // Fallback to native confirm
          if (confirm('Yakin ingin menghapus departemen "' + name + '" ?')) {
            deleteForm.submit();
          }
        }
      });
    });
  });
</script>
@endpush
@endsection

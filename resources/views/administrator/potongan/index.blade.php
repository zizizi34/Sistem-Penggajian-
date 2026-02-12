@extends('layouts.app')

@section('title', 'Data Potongan')
@section('description', 'Kelola Data Potongan')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Potongan</h4>
        <a href="{{ route('administrators.potongan.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
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
                    <a href="{{ route('administrators.potongan.edit', $item->id_potongan) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-potongan" data-action="{{ route('administrators.potongan.destroy', $item->id_potongan) }}" data-name="{{ $item->nama_potongan }}">
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

<!-- Delete confirmation modal -->
<div class="modal fade" id="confirmDeleteModalPotongan" tabindex="-1" aria-labelledby="confirmDeleteLabelPotongan" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabelPotongan">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus potongan <strong id="deleteItemNamePotongan"></strong> ?</p>
      </div>
      <div class="modal-footer">
        <form id="deleteFormPotongan" method="POST" action="">
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
    document.querySelectorAll('.btn-delete-potongan').forEach(function(btn) {
      btn.addEventListener('click', function () {
        var action = this.getAttribute('data-action');
        var name = this.getAttribute('data-name') || '';
        var deleteForm = document.getElementById('deleteFormPotongan');
        var deleteItemName = document.getElementById('deleteItemNamePotongan');
        deleteForm.action = action;
        deleteItemName.textContent = name;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modalEl = document.getElementById('confirmDeleteModalPotongan');
          var modal = new bootstrap.Modal(modalEl);
          modal.show();
        } else {
          if (confirm('Yakin ingin menghapus potongan "' + name + '" ?')) {
            deleteForm.submit();
          }
        }
      });
    });
  });
</script>
@endpush
@endsection

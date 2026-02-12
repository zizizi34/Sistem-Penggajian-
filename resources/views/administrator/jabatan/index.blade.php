@extends('layouts.app')

@section('title', 'Data Jabatan')
@section('description', 'Kelola Data Jabatan')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Jabatan</h4>
        <a href="{{ route('administrators.jabatan.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
      </div>
      <div class="card-body">
        @if($jabatan->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Jabatan</th>
                  <th>Min Gaji</th>
                  <th>Max Gaji</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($jabatan as $item)
                <tr>
                  <td>{{ $item->nama_jabatan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->min_gaji ?? 0, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($item->max_gaji ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <a href="{{ route('administrators.jabatan.edit', $item->id_jabatan) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-jabatan" data-action="{{ route('administrators.jabatan.destroy', $item->id_jabatan) }}" data-name="{{ $item->nama_jabatan }}">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data jabatan</p>
        @endif
      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Delete confirmation modal -->
      <div class="modal fade" id="confirmDeleteModalJabatan" tabindex="-1" aria-labelledby="confirmDeleteLabelJabatan" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteLabelJabatan">Konfirmasi Hapus</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Yakin ingin menghapus jabatan <strong id="deleteItemNameJabatan"></strong> ?</p>
            </div>
            <div class="modal-footer">
              <form id="deleteFormJabatan" method="POST" action="">
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
          document.querySelectorAll('.btn-delete-jabatan').forEach(function(btn) {
            btn.addEventListener('click', function () {
              var action = this.getAttribute('data-action');
              var name = this.getAttribute('data-name') || '';
              var deleteForm = document.getElementById('deleteFormJabatan');
              var deleteItemName = document.getElementById('deleteItemNameJabatan');
              deleteForm.action = action;
              deleteItemName.textContent = name;
              if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modalEl = document.getElementById('confirmDeleteModalJabatan');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
              } else {
                if (confirm('Yakin ingin menghapus jabatan "' + name + '" ?')) {
                  deleteForm.submit();
                }
              }
            });
          });
        });
      </script>
      @endpush
</div>
@endsection

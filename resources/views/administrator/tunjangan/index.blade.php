@extends('layouts.app')

@section('title', 'Data Tunjangan')
@section('description', 'Kelola Data Tunjangan')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Tunjangan</h4>
        <a href="{{ route('administrators.tunjangan.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
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
                    <a href="{{ route('administrators.tunjangan.edit', $item->id_tunjangan) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-tunjangan" data-action="{{ route('administrators.tunjangan.destroy', $item->id_tunjangan) }}" data-name="{{ $item->nama_tunjangan }}">
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
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="confirmDeleteModalTunjangan" tabindex="-1" aria-labelledby="confirmDeleteLabelTunjangan" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabelTunjangan">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus tunjangan <strong id="deleteItemNameTunjangan"></strong> ?</p>
      </div>
      <div class="modal-footer">
        <form id="deleteFormTunjangan" method="POST" action="">
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
    document.querySelectorAll('.btn-delete-tunjangan').forEach(function(btn) {
      btn.addEventListener('click', function () {
        var action = this.getAttribute('data-action');
        var name = this.getAttribute('data-name') || '';
        var deleteForm = document.getElementById('deleteFormTunjangan');
        var deleteItemName = document.getElementById('deleteItemNameTunjangan');
        deleteForm.action = action;
        deleteItemName.textContent = name;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modalEl = document.getElementById('confirmDeleteModalTunjangan');
          var modal = new bootstrap.Modal(modalEl);
          modal.show();
        } else {
          if (confirm('Yakin ingin menghapus tunjangan "' + name + '" ?')) {
            deleteForm.submit();
          }
        }
      });
    });
  });
</script>
@endpush
@endsection

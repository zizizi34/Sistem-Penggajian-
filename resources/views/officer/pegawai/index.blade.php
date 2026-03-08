@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('description', 'Daftar Pegawai Departemen Anda')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Daftar Pegawai</h4>
        <a href="{{ route('officers.pegawai.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah Pegawai
        </a>
      </div>
      <div class="card-body">
        @if($pegawai->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Jabatan</th>
                  <th>Gaji Pokok</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pegawai as $item)
                <tr>
                  <td>{{ $item->nik_pegawai ?? '-' }}</td>
                  <td class="fw-semibold">
                    <a href="{{ route('officers.pegawai.show', $item->id_pegawai) }}" class="text-dark text-decoration-none">
                      {{ $item->nama_pegawai ?? '-' }} <i class="bi bi-box-arrow-up-right ms-1 text-muted small"></i>
                    </a>
                  </td>
                  <td>{{ $item->email_pegawai ?? '-' }}</td>
                  <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge bg-{{ $item->status_pegawai == 'aktif' ? 'success' : 'danger' }}">
                      {{ ucfirst($item->status_pegawai ?? 'aktif') }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('officers.pegawai.edit', $item->id_pegawai) }}"
                       class="btn btn-sm btn-warning"
                       title="Edit Pegawai">
                      <i class="bi bi-pencil"></i>
                    </a>

                    <button type="button"
                            class="btn btn-sm btn-danger"
                            data-delete-action="{{ route('officers.pegawai.destroy', $item->id_pegawai) }}"
                            data-delete-item="{{ $item->nama_pegawai }}"
                            data-delete-category="pegawai"
                            title="Hapus Pegawai">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-1 fw-semibold">Belum ada data pegawai di departemen Anda.</p>
            <small>Klik tombol <strong>Tambah Pegawai</strong> untuk menambahkan data.</small>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')
@endsection

@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('description', 'Kelola Data Pegawai')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Daftar Pegawai</h4>
      </div>
      <div class="card-body">
        @if($pegawai->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Departemen</th>
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
                  <td>{{ $item->nama_pegawai ?? '-' }}</td>
                  <td>{{ $item->email_pegawai ?? '-' }}</td>
                  <td>{{ $item->departemen->nama_departemen ?? '-' }}</td>
                  <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge bg-{{ $item->status_pegawai == 'aktif' ? 'success' : 'danger' }}">
                      {{ ucfirst($item->status_pegawai ?? 'aktif') }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('administrators.pegawai.show', $item->id_pegawai) }}" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i> Lihat
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data pegawai</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

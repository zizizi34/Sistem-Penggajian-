@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('description', 'Daftar Pegawai Departemen Anda')

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
                  <th>Status</th>
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
                  <td>
                    <span class="badge bg-{{ $item->status_pegawai == 'aktif' ? 'success' : 'danger' }}">
                      {{ ucfirst($item->status_pegawai ?? 'aktif') }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data pegawai di departemen Anda</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

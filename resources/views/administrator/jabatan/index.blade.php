@extends('layouts.app')

@section('title', 'Data Jabatan')
@section('description', 'Kelola Data Jabatan')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
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
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.jabatan.destroy', $item->id_jabatan) }}"
                      data-delete-item="{{ $item->nama_jabatan }}"
                      data-delete-category="jabatan">
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

@include('components.delete-confirmation-modal')
@endsection

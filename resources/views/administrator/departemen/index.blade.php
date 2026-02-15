@extends('layouts.app')

@section('title', 'Data Departemen')
@section('description', 'Kelola Data Departemen')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
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
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.departemen.destroy', $item->id_departemen) }}"
                      data-delete-item="{{ $item->nama_departemen }}"
                      data-delete-category="departemen">
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

@include('components.delete-confirmation-modal')
@endsection

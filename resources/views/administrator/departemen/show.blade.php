@extends('layouts.app')

@section('title', 'Detail Departemen')
@section('description', 'Detail Departemen')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Detail Departemen</h4>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Departemen</label>
          <p>{{ $departemen->nama_departemen ?? '-' }}</p>
        </div>

        <div class="mb-3">
          <label class="form-label">Manager Departemen</label>
          <p>{{ $departemen->manager_departemen ?? '-' }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Aksi</h4>
      </div>
      <div class="card-body">
        <a href="{{ route('administrators.departemen.edit', $departemen->id_departemen) }}" class="btn btn-warning w-100 mb-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('administrators.departemen.destroy', $departemen->id_departemen) }}" method="POST" class="d-inline-block w-100">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Yakin ingin menghapus?')">
            <i class="bi bi-trash"></i> Hapus
          </button>
        </form>
        <a href="{{ route('administrators.departemen.index') }}" class="btn btn-secondary w-100">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Departemen')
@section('description', 'Tambah Departemen Baru')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Tambah Departemen</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.departemen.store') }}" method="POST">
          @csrf
          
          <div class="mb-3">
            <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
            <input type="text" name="nama_departemen" class="form-control @error('nama_departemen') is-invalid @enderror" value="{{ old('nama_departemen') }}" required>
            @error('nama_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Manager</label>
            <input type="number" name="manager_departemen" class="form-control @error('manager_departemen') is-invalid @enderror" value="{{ old('manager_departemen') }}">
            @error('manager_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
            <div class="col-md-6">
              <a href="{{ route('administrators.departemen.index') }}" class="btn btn-secondary w-100">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

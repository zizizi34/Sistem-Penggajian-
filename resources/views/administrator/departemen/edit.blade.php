@extends('layouts.app')

@section('title', 'Edit Departemen')
@section('description', 'Edit Departemen')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Edit Departemen</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.departemen.update', $departemen->id_departemen) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
            <input type="text" name="nama_departemen" class="form-control @error('nama_departemen') is-invalid @enderror" value="{{ old('nama_departemen', $departemen->nama_departemen) }}" required>
            @error('nama_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Manager</label>
            <input type="number" name="manager_departemen" class="form-control @error('manager_departemen') is-invalid @enderror" value="{{ old('manager_departemen', $departemen->manager_departemen) }}">
            @error('manager_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100">Update</button>
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

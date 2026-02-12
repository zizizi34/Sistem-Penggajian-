@extends('layouts.app')

@section('title', 'Edit Jabatan')
@section('description', 'Edit Jabatan')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Edit Jabatan</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.jabatan.update', $jabatan->id_jabatan) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
            <input type="text" name="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror" value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required>
            @error('nama_jabatan')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Min Gaji <span class="text-danger">*</span></label>
            <input type="number" name="min_gaji" class="form-control @error('min_gaji') is-invalid @enderror" value="{{ old('min_gaji', $jabatan->min_gaji) }}" required>
            @error('min_gaji')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Max Gaji <span class="text-danger">*</span></label>
            <input type="number" name="max_gaji" class="form-control @error('max_gaji') is-invalid @enderror" value="{{ old('max_gaji', $jabatan->max_gaji) }}" required>
            @error('max_gaji')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Departemen</label>
            @if(isset($departemen) && $departemen->count() > 0)
              <select name="id_departemen" class="form-control @error('id_departemen') is-invalid @enderror">
                <option value="">-- Pilih Departemen --</option>
                @foreach($departemen as $d)
                  <option value="{{ $d->id_departemen }}" {{ (old('id_departemen', $jabatan->id_departemen) == $d->id_departemen) ? 'selected' : '' }}>{{ $d->nama_departemen }}</option>
                @endforeach
              </select>
            @else
              <p class="text-muted">Departemen belum tersedia</p>
            @endif
            @error('id_departemen')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100">Update</button>
            </div>
            <div class="col-md-6">
              <a href="{{ route('administrators.jabatan.index') }}" class="btn btn-secondary w-100">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

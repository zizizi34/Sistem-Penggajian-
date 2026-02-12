@extends('layouts.app')

@section('title', 'Tambah Tunjangan')
@section('description', 'Tambah Tunjangan Baru')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Tambah Tunjangan</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.tunjangan.store') }}" method="POST">
          @csrf
          
          <div class="mb-3">
            <label class="form-label">Nama Tunjangan <span class="text-danger">*</span></label>
            <input type="text" name="nama_tunjangan" class="form-control @error('nama_tunjangan') is-invalid @enderror" value="{{ old('nama_tunjangan') }}" required>
            @error('nama_tunjangan')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Nominal <span class="text-danger">*</span></label>
            <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal') }}" required>
            @error('nominal')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
            <div class="col-md-6">
              <a href="{{ route('administrators.tunjangan.index') }}" class="btn btn-secondary w-100">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Potongan')
@section('description', 'Tambah Potongan Baru')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Tambah Potongan</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.potongan.store') }}" method="POST">
          @csrf
          
          <div class="mb-3">
            <label class="form-label">Nama Potongan <span class="text-danger">*</span></label>
            <input type="text" name="nama_potongan" class="form-control @error('nama_potongan') is-invalid @enderror" value="{{ old('nama_potongan') }}" required>
            @error('nama_potongan')
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
              <a href="{{ route('administrators.potongan.index') }}" class="btn btn-secondary w-100">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

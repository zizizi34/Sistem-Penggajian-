@extends('layouts.app')

@section('title', 'Edit Tunjangan')
@section('description', 'Edit Tunjangan')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Edit Tunjangan</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.tunjangan.update', $tunjangan->id_tunjangan) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label class="form-label">Nama Tunjangan <span class="text-danger">*</span></label>
            <input type="text" name="nama_tunjangan" class="form-control @error('nama_tunjangan') is-invalid @enderror" value="{{ old('nama_tunjangan', $tunjangan->nama_tunjangan) }}" required>
            @error('nama_tunjangan')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Nominal <span class="text-danger">*</span></label>
            <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $tunjangan->nominal) }}" required>
            @error('nominal')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100">Update</button>
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

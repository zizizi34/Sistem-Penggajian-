@extends('layouts.app')

@section('title', 'Edit Status PTKP')
@section('description', 'Edit Status PTKP')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Form Edit Status PTKP</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('administrators.ptkp-status.update', $ptkpStatus->id_ptkp_status) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label class="form-label">Kode PTKP</label>
            <input type="text" name="kode_ptkp_status" class="form-control @error('kode_ptkp_status') is-invalid @enderror" value="{{ old('kode_ptkp_status', $ptkpStatus->kode_ptkp_status) }}">
            @error('kode_ptkp_status')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" value="{{ old('deskripsi', $ptkpStatus->deskripsi) }}">
            @error('deskripsi')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Nominal <span class="text-danger">*</span></label>
            <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $ptkpStatus->nominal) }}" required>
            @error('nominal')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary w-100">Update</button>
            </div>
            <div class="col-md-6">
              <a href="{{ route('administrators.ptkp-status.index') }}" class="btn btn-secondary w-100">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

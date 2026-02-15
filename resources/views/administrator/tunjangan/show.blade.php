@extends('layouts.app')

@section('title', 'Detail Tunjangan')
@section('description', 'Detail Tunjangan')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Detail Tunjangan</h4>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Tunjangan</label>
          <p>{{ $tunjangan->nama_tunjangan ?? '-' }}</p>
        </div>

        <div class="mb-3">
          <label class="form-label">Nominal</label>
          <p>Rp {{ number_format($tunjangan->nominal ?? 0, 0, ',', '.') }}</p>
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
        <a href="{{ route('administrators.tunjangan.edit', $tunjangan->id_tunjangan) }}" class="btn btn-warning w-100 mb-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('administrators.tunjangan.destroy', $tunjangan->id_tunjangan) }}" method="POST" class="d-inline-block w-100">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Yakin ingin menghapus?')">
            <i class="bi bi-trash"></i> Hapus
          </button>
        </form>
        <a href="{{ route('administrators.tunjangan.index') }}" class="btn btn-secondary w-100">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

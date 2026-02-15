@extends('layouts.app')

@section('title', 'Detail Potongan')
@section('description', 'Detail Potongan')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Detail Potongan</h4>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Potongan</label>
          <p>{{ $potongan->nama_potongan ?? '-' }}</p>
        </div>

        <div class="mb-3">
          <label class="form-label">Nominal</label>
          <p>Rp {{ number_format($potongan->nominal ?? 0, 0, ',', '.') }}</p>
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
        <a href="{{ route('administrators.potongan.edit', $potongan->id_potongan) }}" class="btn btn-warning w-100 mb-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('administrators.potongan.destroy', $potongan->id_potongan) }}" method="POST" class="d-inline-block w-100">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Yakin ingin menghapus?')">
            <i class="bi bi-trash"></i> Hapus
          </button>
        </form>
        <a href="{{ route('administrators.potongan.index') }}" class="btn btn-secondary w-100">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

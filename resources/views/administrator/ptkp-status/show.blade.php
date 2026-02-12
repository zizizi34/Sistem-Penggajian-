@extends('layouts.app')

@section('title', 'Detail Status PTKP')
@section('description', 'Detail Status PTKP')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Detail Status PTKP</h4>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Kode PTKP</label>
          <p>{{ $ptkpStatus->kode_ptkp_status ?? '-' }}</p>
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <p>{{ $ptkpStatus->deskripsi ?? '-' }}</p>
        </div>

        <div class="mb-3">
          <label class="form-label">Nominal</label>
          <p>Rp {{ number_format($ptkpStatus->nominal ?? 0, 0, ',', '.') }}</p>
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
        <a href="{{ route('administrators.ptkp-status.edit', $ptkpStatus->id_ptkp_status) }}" class="btn btn-warning w-100 mb-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
        <form action="{{ route('administrators.ptkp-status.destroy', $ptkpStatus->id_ptkp_status) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Yakin ingin menghapus?')">
            <i class="bi bi-trash"></i> Hapus
          </button>
        </form>
        <a href="{{ route('administrators.ptkp-status.index') }}" class="btn btn-secondary w-100">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

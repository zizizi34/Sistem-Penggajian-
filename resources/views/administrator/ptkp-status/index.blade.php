@extends('layouts.app')

@section('title', 'Data Status PTKP')
@section('description', 'Kelola Data Status PTKP')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Status PTKP</h4>
        <a href="{{ route('administrators.ptkp-status.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
      </div>
      <div class="card-body">
        @if($ptkpStatus->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Kode</th>
                  <th>Deskripsi</th>
                  <th>Nominal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ptkpStatus as $item)
                <tr>
                  <td>{{ $item->kode_ptkp_status ?? '-' }}</td>
                  <td>{{ $item->deskripsi ?? '-' }}</td>
                  <td>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <a href="{{ route('administrators.ptkp-status.show', $item->id_ptkp_status) }}" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('administrators.ptkp-status.edit', $item->id_ptkp_status) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.ptkp-status.destroy', $item->id_ptkp_status) }}"
                      data-delete-item="{{ $item->deskripsi ?? $item->kode_ptkp_status }}"
                      data-delete-category="status PTKP">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data status PTKP</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')
@endsection

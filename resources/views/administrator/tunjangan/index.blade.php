@extends('layouts.app')

@section('title', 'Data Tunjangan')
@section('description', 'Kelola Data Tunjangan')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Tunjangan</h4>
        <a href="{{ route('administrators.tunjangan.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
      </div>
      <div class="card-body">
        @if($tunjangan->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Tunjangan</th>
                  <th>Nominal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tunjangan as $item)
                <tr>
                  <td>{{ $item->nama_tunjangan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <a href="{{ route('administrators.tunjangan.edit', $item->id_tunjangan) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.tunjangan.destroy', $item->id_tunjangan) }}"
                      data-delete-item="{{ $item->nama_tunjangan }}"
                      data-delete-category="tunjangan">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data tunjangan</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')
@endsection

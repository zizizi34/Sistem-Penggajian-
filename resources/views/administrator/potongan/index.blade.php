@extends('layouts.app')

@section('title', 'Data Potongan')
@section('description', 'Kelola Data Potongan')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Potongan</h4>
        <a href="{{ route('administrators.potongan.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Tambah
        </a>
      </div>
      <div class="card-body">
        @if($potongan->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Potongan</th>
                  <th>Nominal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($potongan as $item)
                <tr>
                  <td>{{ $item->nama_potongan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <a href="{{ route('administrators.potongan.edit', $item->id_potongan) }}" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger" 
                      data-delete-action="{{ route('administrators.potongan.destroy', $item->id_potongan) }}"
                      data-delete-item="{{ $item->nama_potongan }}"
                      data-delete-category="potongan">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data potongan</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')
@endsection
@endsection

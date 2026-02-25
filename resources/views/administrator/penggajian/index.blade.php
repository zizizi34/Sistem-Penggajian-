@extends('layouts.app')

@section('title', 'Data Penggajian')
@section('description', 'Kelola Data Penggajian')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Penggajian</h4>
        <form action="{{ route('administrators.penggajian.calculate') }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="month" name="periode" class="form-control form-control-sm" value="{{ now()->format('Y-m') }}">
            <button type="submit" class="btn btn-primary btn-sm text-nowrap">
                <i class="bi bi-calculator"></i> Hitung Gaji
            </button>
        </form>
      </div>
      <div class="card-body">
        @if($penggajian->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Pegawai</th>
                  <th>Periode</th>
                  <th>Gaji Pokok</th>
                  <th>Tunjangan</th>
                  <th>Potongan</th>
                  <th>Gaji Bersih</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($penggajian as $item)
                <tr>
                  <td>{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                  <td>{{ $item->periode ?? '-' }}</td>
                  <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($item->total_tunjangan ?? 0, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($item->total_potongan ?? 0, 0, ',', '.') }}</td>
                  <td>Rp {{ number_format($item->gaji_bersih ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge bg-{{ $item->status == 'draft' ? 'warning' : 'success' }}">
                      {{ ucfirst($item->status ?? 'draft') }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('administrators.penggajian.show', $item->id_penggajian) }}" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i> Lihat
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data penggajian</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

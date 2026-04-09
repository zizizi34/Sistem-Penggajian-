@extends('layouts.app')

@section('title', 'Data Absensi Tim Saya')
@section('description', 'Kelola Data Absensi')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Absensi Tim Saya</h4>
      </div>
      <div class="card-body">
        <form action="{{ route('officers.absensi.index') }}" method="GET" class="mb-4">
          <div class="row g-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label text-muted small fw-bold text-uppercase">Dari Tanggal</label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event"></i></span>
                <input type="date" name="tanggal_dari" class="form-control border-start-0" value="{{ request('tanggal_dari') }}">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label text-muted small fw-bold text-uppercase">Sampai Tanggal</label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-check"></i></span>
                <input type="date" name="tanggal_sampai" class="form-control border-start-0" value="{{ request('tanggal_sampai') }}">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label text-muted small fw-bold text-uppercase">Status</label>
              <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
              </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-filter me-1"></i> Filter
              </button>
              <a href="{{ route('officers.absensi.index') }}" class="btn btn-light-secondary flex-grow-1">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
              </a>
            </div>
          </div>
        </form>

        @if($absensi->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Pegawai</th>
                  <th>Tanggal</th>
                  <th>Jam Masuk</th>
                  <th>Jam Pulang</th>
                  <th>Sistem Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($absensi as $item)
                <tr>
                  <td>{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                  <td>{{ $item->tanggal_absensi }}</td>
                  <td>{{ $item->jam_masuk ?? '-' }}</td>
                  <td>{{ $item->jam_pulang ?? '-' }}</td>
                  <td>
                    <span class="badge bg-{{ match(strtolower($item->status)) {
                      'hadir' => 'success',
                      'izin', 'sakit' => 'warning',
                      'alpha' => 'danger',
                      default => 'secondary'
                    } }}">
                      {{ ucfirst($item->status ?? 'alpha') }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-4 d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted small">
                Showing {{ $absensi->firstItem() }} to {{ $absensi->lastItem() }} of {{ $absensi->total() }} results
              </p>
            </div>
            <div>
              {{ $absensi->links() }}
            </div>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data absensi untuk tim Anda.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

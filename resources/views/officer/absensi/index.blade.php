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
                  <th>Persetujuan (Status)</th>
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
                    <span class="badge bg-{{ $item->status == 'hadir' ? 'success' : ($item->status == 'izin' ? 'warning' : 'danger') }}">
                      {{ ucfirst($item->status ?? 'alpha') }}
                    </span>
                  </td>
                  <td>
                    <span class="badge bg-{{ \Carbon\Carbon::parse($item->approved_at)->isValid() ? 'primary' : 'secondary' }}">
                      {{ \Carbon\Carbon::parse($item->approved_at)->isValid() ? 'Telah Disetujui' : 'Menunggu Persetujuan' }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data absensi untuk tim Anda.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Data Lembur Tim Saya')
@section('description', 'Kelola Data Lembur')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Lembur Tim Saya</h4>
      </div>
      <div class="card-body">
        @if($lembur->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Pegawai</th>
                  <th>Tanggal</th>
                  <th>Mulai</th>
                  <th>Selesai</th>
                  <th>Dursi (Jam)</th>
                  <th>Keterangan</th>
                  <th>Persetujuan (Status)</th>
                </tr>
              </thead>
              <tbody>
                @foreach($lembur as $item)
                <tr>
                  <td>{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                  <td>{{ $item->tanggal_lembur }}</td>
                  <td>{{ $item->jam_mulai ?? '-' }}</td>
                  <td>{{ $item->jam_selesai ?? '-' }}</td>
                  <td>{{ $item->durasi ?? '-' }}</td>
                  <td>{{ Str::limit($item->keterangan, 50) ?? '-' }}</td>
                  <td>
                    <span class="badge bg-{{ $item->status == 'approved' ? 'primary' : 'secondary' }}">
                      {{ $item->status == 'approved' ? 'Telah Disetujui' : 'Menunggu Persetujuan' }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data lembur untuk tim Anda.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

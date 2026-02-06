@extends('layouts.app')

@section('title', 'Beranda')
@section('description', 'Halaman Beranda')

@section('content')
<section class="row">
  <div class="col-12 col-lg-9">
    <div class="row">
      <div class="col-6 col-lg-4 col-md-6">
        <div class="card">
          <a href="{{ route('officers.pegawai.index') }}">
            <div class="card-body px-4 py-4-5">
              <div class="row">
                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                  <div class="stats-icon blue mb-2">
                    <i class="iconly-boldProfile"></i>
                  </div>
                </div>
                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                  <h6 class="text-muted font-semibold">Total Pegawai</h6>
                  <h6 class="font-extrabold mb-0">{{ $totalPegawai ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-6 col-lg-4 col-md-6">
        <div class="card">
          <a href="{{ route('officers.departemen.index') }}">
            <div class="card-body px-4 py-4-5">
              <div class="row">
                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                  <div class="stats-icon green mb-2">
                    <i class="iconly-boldWork"></i>
                  </div>
                </div>
                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                  <h6 class="text-muted font-semibold">Total Departemen</h6>
                  <h6 class="font-extrabold mb-0">{{ $totalDepartemen ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-6 col-lg-4 col-md-6">
        <div class="card">
          <a href="{{ route('officers.penggajian.index') }}">
            <div class="card-body px-4 py-4-5">
              <div class="row">
                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                  <div class="stats-icon red mb-2">
                    <i class="iconly-boldChart"></i>
                  </div>
                </div>
                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                  <h6 class="text-muted font-semibold">Total Penggajian</h6>
                  <h6 class="font-extrabold mb-0">{{ $totalPenggajian ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>Penggajian Terbaru</h4>
          </div>
          <div class="card-body">
            @if($recentPenggajian->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover table-lg">
                  <thead>
                    <tr>
                      <th>Nama Pegawai</th>
                      <th>Periode</th>
                      <th>Gaji Pokok</th>
                      <th>Total Tunjangan</th>
                      <th>Total Potongan</th>
                      <th>Gaji Bersih</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($recentPenggajian as $item)
                    <tr>
                      <td>{{ $item->pegawai->nama ?? '-' }}</td>
                      <td>{{ $item->periode ?? '-' }}</td>
                      <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                      <td>Rp {{ number_format($item->total_tunjangan ?? 0, 0, ',', '.') }}</td>
                      <td>Rp {{ number_format($item->total_potongan ?? 0, 0, ',', '.') }}</td>
                      <td>Rp {{ number_format($item->gaji_bersih ?? 0, 0, ',', '.') }}</td>
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
  </div>

  <div class="col-12 col-lg-3">
    <div class="card">
      <div class="card-header">
        <h4>Statistik</h4>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <span>Pegawai</span>
          <strong>{{ $totalPegawai ?? 0 }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Departemen</span>
          <strong>{{ $totalDepartemen ?? 0 }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Jabatan</span>
          <strong>{{ $totalJabatan ?? 0 }}</strong>
        </div>
        <div class="d-flex justify-content-between">
          <span>Penggajian</span>
          <strong>{{ $totalPenggajian ?? 0 }}</strong>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
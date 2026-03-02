@extends('layouts.app')

@section('title', 'Beranda')
@section('description', 'Halaman Beranda')
@section('content')
{{-- ===== STAT CARDS - Full Width Row ===== --}}
<div class="row mb-3">
  <div class="col-6 col-lg-3 col-md-6">
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
  <div class="col-6 col-lg-3 col-md-6">
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
  <div class="col-6 col-lg-3 col-md-6">
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
  <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
      <div class="card-body px-4 py-4-5">
        <div class="row">
          <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
            <div class="stats-icon purple mb-2">
              <i class="iconly-boldTime-Circle"></i>
            </div>
          </div>
          <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
            <h6 class="text-muted font-semibold">Total Hadir</h6>
            <h6 class="font-extrabold mb-0">{{ $totalHadir ?? 0 }}</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== MAIN CONTENT + SIDEBAR ===== --}}
<section class="row">
  <div class="col-12 col-lg-9">

    <div class="row">

      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>Absensi Hari Ini</h4>
          </div>
          <div class="card-body">
            @if($recentAbsensi->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover table-lg">
                  <thead>
                    <tr>
                      <th>Nama Pegawai</th>
                      <th>Jam Masuk</th>
                      <th>Jam Pulang</th>
                      <th>Foto</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($recentAbsensi as $item)
                    <tr>
                      <td>{{ $item->pegawai?->nama_pegawai ?? '-' }}</td>
                      <td>{{ $item->jam_masuk }}</td>
                      <td>{{ $item->jam_pulang ?? '-' }}</td>
                      <td>
                          @if($item->foto_masuk)
                            <a href="{{ Storage::url($item->foto_masuk) }}" target="_blank">Lihat</a>
                          @else
                            -
                          @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <p class="text-center text-muted">Belum ada data absensi hari ini</p>
            @endif
          </div>
        </div>
      </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6">
        <div class="card">
          <div class="card-header">
            <h4>Pegawai Terlambat (Hari Ini)</h4>
          </div>
          <div class="card-body">
            @if(count($terlambatList) > 0)
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Nama Pegawai</th>
                      <th>Jam Masuk</th>
                      <th>Keterlambatan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($terlambatList as $ab)
                    <tr>
                      <td>{{ $ab->pegawai?->nama_pegawai ?? '-' }}</td>
                      <td>{{ $ab->jam_masuk }}</td>
                      <td><span class="badge bg-danger">{{ $ab->terlambat_menit }} Menit</span></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <p class="text-center text-muted">Tidak ada pegawai yang terlambat hari ini</p>
            @endif
          </div>
        </div>
      </div>
      
      <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center" 
               style="background: {{ count($overtimeList ?? []) > 0 ? 'linear-gradient(135deg, #ff6b6b, #ee5a24)' : '' }}; {{ count($overtimeList ?? []) > 0 ? 'color: white;' : '' }}">
            <h4 class="mb-0" style="{{ count($overtimeList ?? []) > 0 ? 'color: white;' : '' }}">
              <i class="bi bi-clock-history me-1"></i>
              Sedang Lembur Sekarang
            </h4>
            @if(count($overtimeList ?? []) > 0)
              <span class="badge bg-white text-danger fw-bold" style="font-size:0.85rem;">
                🔴 {{ count($overtimeList) }} Pegawai
              </span>
            @endif
          </div>
          <div class="card-body p-0">
            @if(count($overtimeList ?? []) > 0)
              {{-- Alert notifikasi --}}
              <div class="alert alert-danger m-3 mb-0 py-2 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>{{ count($overtimeList) }} pegawai</strong>&nbsp;masih di kantor melewati jam pulang jadwal!
              </div>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Nama Pegawai</th>
                      <th>Jam Masuk</th>
                      <th>Sudah Lembur</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($overtimeList as $ot)
                    @php
                      $menit = $ot->overtime_menit;
                      $jam   = intdiv($menit, 60);
                      $sisa  = $menit % 60;
                      $label = ($jam > 0 ? $jam . ' jam ' : '') . $sisa . ' mnt';
                    @endphp
                    <tr>
                      <td class="fw-semibold">{{ $ot->pegawai?->nama_pegawai ?? '-' }}</td>
                      <td>{{ $ot->jam_masuk }}</td>
                      <td>
                        <span class="badge bg-danger">
                          ⏱ {{ $label }}
                        </span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div class="px-3 py-2 text-muted" style="font-size:0.78rem;">
                <i class="bi bi-info-circle"></i> Diperbarui saat halaman dimuat. Refresh untuk data terbaru.
              </div>
            @else
              <div class="text-center py-4 text-muted">
                <i class="bi bi-check-circle-fill text-success" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0">Semua pegawai sudah pulang atau belum ada yang lembur hari ini.</p>
              </div>
            @endif
          </div>
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
                      <td>{{ $item->pegawai?->nama_pegawai ?? '-' }}</td>
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
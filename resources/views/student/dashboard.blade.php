@extends('layouts.app')

@section('title', 'Beranda')
@section('description', 'Halaman Beranda')

@section('content')
<section class="row">
  <div class="col-12 col-lg-9">
    <div class="row">
      <div class="col-6 col-lg-6 col-md-6">
        <div class="card">
          <a href="{{ route('students.payroll.index') }}">
            <div class="card-body px-4 py-4-5">
              <div class="row">
                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                  <div class="stats-icon blue mb-2">
                    <i class="iconly-boldChart"></i>
                  </div>
                </div>
                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                  <h6 class="text-muted font-semibold">Slip Gaji Saya</h6>
                  <h6 class="font-extrabold mb-0">{{ $myPayrollCount ?? 0 }}</h6>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-6 col-lg-6 col-md-6">
        <div class="card">
          <a href="{{ route('students.attendance.index') }}">
            <div class="card-body px-4 py-4-5">
              <div class="row">
                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                  <div class="stats-icon green mb-2">
                    <i class="iconly-boldProfile"></i>
                  </div>
                </div>
                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                  <h6 class="text-muted font-semibold">Absensi Saya</h6>
                  <h6 class="font-extrabold mb-0">{{ $myAttendanceCount ?? 0 }}</h6>
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
            <h4>Slip Gaji Terbaru</h4>
          </div>
          <div class="card-body">
            @if($myPayrolls->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover table-lg">
                  <thead>
                    <tr>
                      <th>Periode</th>
                      <th>Gaji Pokok</th>
                      <th>Total Tunjangan</th>
                      <th>Total Potongan</th>
                      <th>Gaji Bersih</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($myPayrolls as $item)
                    <tr>
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
              <p class="text-center text-muted">Belum ada data slip gaji</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-3">
    <div class="card">
      <div class="card-body py-4 px-4">
        <div class="d-flex align-items-center">
          <div class=" ms-3 name">
            <h5 class="font-bold">{{ auth('student')->user()->name }}</h5>
            <h6 class="text-muted mb-0">{{ auth('student')->user()->email }}</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h4>Ringkasan</h4>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <span>Slip Gaji</span>
          <strong>{{ $myPayrollCount ?? 0 }}</strong>
        </div>
        <div class="d-flex justify-content-between">
          <span>Absensi</span>
          <strong>{{ $myAttendanceCount ?? 0 }}</strong>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

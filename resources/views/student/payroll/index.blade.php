@extends('layouts.app')

@section('title', 'Slip Gaji Saya')
@section('description', 'Riwayat Penggajian dan Slip Gaji Bulanan Anda')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
              <div>
                <h4 class="mb-0"><i class="bi bi-wallet2 me-2 text-primary"></i>Slip Gaji Saya</h4>
                <small class="text-muted">Gaji dihitung bulanan & ditransfer di akhir bulan</small>
              </div>
            </div>
            <div class="card-body">
                @if($payrolls->isEmpty())
                    <div class="text-center py-5">
                      <i class="bi bi-inbox text-muted" style="font-size:3rem"></i>
                      <p class="text-muted mt-2">Belum ada data penggajian.</p>
                      <p class="text-muted small">Data akan muncul setelah admin memproses penggajian bulan ini.</p>
                    </div>
                @else
                    {{-- Summary kartu terakhir --}}
                    @php $latest = $payrolls->first(); @endphp
                    <div class="row mb-4 g-3">
                      <div class="col-md-4">
                        <div class="bg-primary bg-opacity-10 rounded p-3 text-center border border-primary border-opacity-25">
                          <div class="fw-bold text-primary fs-5">{{ $latest->periode }}</div>
                          <small class="text-muted">Periode Terkini</small>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="bg-success bg-opacity-10 rounded p-3 text-center border border-success border-opacity-25">
                          <div class="fw-bold text-success fs-5">Rp {{ number_format($latest->gaji_bersih, 0, ',', '.') }}</div>
                          <small class="text-muted">Gaji Bersih Terkini</small>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="bg-info bg-opacity-10 rounded p-3 text-center border border-info border-opacity-25">
                          <div class="fw-bold text-info fs-5">{{ $payrolls->count() }} Bulan</div>
                          <small class="text-muted">Total Riwayat</small>
                        </div>
                      </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Periode</th>
                                    <th>Gaji Pokok</th>
                                    <th>Tunjangan</th>
                                    <th>Lembur</th>
                                    <th>Potongan</th>
                                    <th>Pajak (PPH21)</th>
                                    <th class="text-success">Gaji Bersih</th>
                                    <th>Tgl Transfer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrolls as $index => $payroll)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge bg-secondary">{{ $payroll->periode }}</span></td>
                                        <td>Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                                        <td class="text-success">+ Rp {{ number_format($payroll->total_tunjangan, 0, ',', '.') }}</td>
                                        <td class="text-info">+ Rp {{ number_format($payroll->lembur, 0, ',', '.') }}</td>
                                        <td class="text-danger">- Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
                                        <td class="text-danger">- Rp {{ number_format($payroll->pajak_pph21, 0, ',', '.') }}</td>
                                        <td class="fw-bold text-success">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                                        <td>
                                          @if($payroll->tanggal_transfer)
                                            <small>{{ \Carbon\Carbon::parse($payroll->tanggal_transfer)->translatedFormat('d M Y') }}</small>
                                          @else
                                            <small class="text-muted">-</small>
                                          @endif
                                        </td>
                                        <td>
                                            @if($payroll->status == 'paid')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Dibayar</span>
                                            @elseif($payroll->status == 'pending')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($payroll->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

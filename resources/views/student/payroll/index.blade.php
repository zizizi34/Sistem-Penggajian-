@extends('layouts.app')

@section('title', 'Slip Gaji')
@section('description', 'Riwayat Penggajian dan Slip Gaji Anda')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Riwayat Penggajian</h4>
            </div>
            <div class="card-body">
                @if($payrolls->isEmpty())
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i> Belum ada data penggajian.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Periode</th>
                                    <th>Gaji Pokok</th>
                                    <th>Tunjangan</th>
                                    <th>Potongan</th>
                                    <th>Lembur</th>
                                    <th>Pajak (PPH21)</th>
                                    <th>Gaji Bersih</th>
                                    <th>Tanggal Transfer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrolls as $index => $payroll)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $payroll->periode }}</td>
                                        <td>Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($payroll->total_tunjangan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($payroll->lembur, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($payroll->pajak_pph21, 0, ',', '.') }}</td>
                                        <td class="fw-bold text-success">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                                        <td>{{ $payroll->tanggal_transfer ? \Carbon\Carbon::parse($payroll->tanggal_transfer)->translatedFormat('d M Y') : '-' }}</td>
                                        <td>
                                            @if($payroll->status == 'dibayar')
                                                <span class="badge bg-success">Dibayar</span>
                                            @elseif($payroll->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
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

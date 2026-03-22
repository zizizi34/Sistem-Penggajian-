@extends('layouts.app')

@section('title', 'Detail Slip Gaji')
@section('description', 'Lihat rician detail gaji bulanan Anda')

@section('content')

{{-- Print-only CSS --}}
<style>
  @media print {
    body * { visibility: hidden !important; }
    #printable-slip, #printable-slip * { visibility: visible !important; }
    #printable-slip {
      position: absolute !important;
      left: 0; top: 0;
      width: 100% !important;
      margin: 0 !important;
      padding: 20px !important;
      box-shadow: none !important;
    }
    #sidebar, header, footer, .page-title, .no-print { display: none !important; }
    #main { margin: 0 !important; padding: 0 !important; }
    #app { display: block !important; }
    body { background: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .card { border: none !important; box-shadow: none !important; }
    .slip-table { border-collapse: collapse; width: 100%; }
    .slip-table th, .slip-table td { border: 1px solid #dee2e6 !important; padding: 8px 12px !important; }
    .slip-table thead th { background-color: #f8f9fa !important; }
    @page { size: A4 portrait; margin: 15mm; }
  }

  /* Screen styles for the slip card */
  .slip-header-bar {
    background: linear-gradient(135deg, #435ebe 0%, #617ae2 100%);
    color: #fff;
    padding: 25px 30px;
    border-radius: 12px 12px 0 0;
  }
  .slip-company-name { font-size: 1.5rem; font-weight: 800; letter-spacing: 0.5px; }
  .slip-company-sub { font-size: 0.85rem; opacity: 0.85; }
  .slip-title-bar {
    background: #f8f9ff;
    border-bottom: 2px solid #435ebe;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .slip-info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px 30px; padding: 25px 30px; background: #fff; }
  .slip-info-label { font-size: 0.7rem; text-transform: uppercase; color: #6c757d; font-weight: 700; letter-spacing: 0.8px; margin-bottom: 4px; }
  .slip-info-value { font-size: 1rem; font-weight: 600; color: #25396f; }
  .slip-section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; padding: 12px 30px; margin: 0; }
  .slip-table { width: 100%; border-collapse: collapse; }
  .slip-table th, .slip-table td { padding: 12px 30px; font-size: 0.95rem; }
  .slip-table tbody tr { border-bottom: 1px solid #f0f3ff; }
  .slip-table tbody tr:last-child { border-bottom: none; }
  .slip-table .amount { text-align: right; font-weight: 700; white-space: nowrap; font-family: 'Courier New', Courier, monospace; }
  .slip-table .label-col { color: #4b4b4b; font-weight: 500; }
  .slip-total-row { background: #fcfdff; font-weight: 800; border-top: 2px dashed #d1d9e6 !important; }
  .slip-netto-bar {
    background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    color: #fff;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .slip-footer {
    padding: 20px 30px;
    background: #fdfdfd;
    border-top: 1px solid #e9ecef;
    font-size: 0.8rem;
    color: #8892a0;
    border-radius: 0 0 12px 12px;
  }
  .slip-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(67, 94, 190, 0.1); }
</style>

<div class="row">
  <div class="col-lg-9 col-md-12">
    {{-- === SLIP START === --}}
    <div id="printable-slip" class="slip-card mb-4 bg-white">

      {{-- Header --}}
      <div class="slip-header-bar">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="slip-company-name">LAGUNA GROUP</div>
            <div class="slip-company-sub">Sistem Penggajian Pegawai & Absensi Terpadu</div>
          </div>
          <div class="text-end">
            <div style="font-size:0.9rem;opacity:0.8;font-weight:600;">E-PAY SLIP</div>
            <div style="font-size:1.4rem;font-weight:900;">{{ $penggajian->periode }}</div>
          </div>
        </div>
      </div>

      {{-- Title Bar --}}
      <div class="slip-title-bar">
        <div style="font-weight:700;color:#435ebe;font-size:1.1rem">
          <i class="bi bi-shield-check me-2"></i> Rincian Penghasilan Bulanan
        </div>
        @if($penggajian->status == 'paid')
          <span class="badge bg-success px-4 py-2" style="font-size:0.85rem"><i class="bi bi-check-all me-1"></i>PEMBAYARAN SELESAI</span>
        @else
          <span class="badge bg-warning text-dark px-4 py-2" style="font-size:0.85rem"><i class="bi bi-hourglass-split me-1"></i>PROSES ADMINISTRASI</span>
        @endif
      </div>

      {{-- Info Pegawai --}}
      <div class="slip-info-grid">
        <div>
          <div class="slip-info-label">Nama Lengkap</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->nama_pegawai }}</div>
        </div>
        <div>
          <div class="slip-info-label">ID Pegawai</div>
          <div class="slip-info-value">#{{ str_pad($penggajian->id_pegawai, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div>
          <div class="slip-info-label">Departemen</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->departemen->nama_departemen ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">Jabatan</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->jabatan->nama_jabatan ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">Tanggal Terbit</div>
          <div class="slip-info-value">{{ $penggajian->created_at->translatedFormat('d F Y') }}</div>
        </div>
        <div>
          <div class="slip-info-label">Status Payroll</div>
          <div class="slip-info-value text-success">Verified</div>
        </div>
      </div>

      {{-- Kehadiran (Stats) --}}
      <div class="slip-section-title text-secondary" style="background:#fcfcfc;border-top:1px solid #eef2ff;">
        <i class="bi bi-calendar-check me-2"></i> Ringkasan Presensi & Kehadiran
      </div>
      <div class="px-4 py-3 border-bottom d-flex gap-5">
        <div class="text-center">
            <div class="small text-muted mb-1">MASUK (HADIR)</div>
            <div class="fw-bold fs-5 text-success">{{ $stats['hadir'] }} Hari</div>
        </div>
        <div class="text-center">
            <div class="small text-muted mb-1">IZIN / SAKIT</div>
            <div class="fw-bold fs-5 text-info">{{ $stats['izin'] }} Hari</div>
        </div>
        <div class="text-center">
            <div class="small text-muted mb-1">ALPHA</div>
            <div class="fw-bold fs-5 text-danger">{{ $stats['alpha'] }} Hari</div>
        </div>
      </div>

      {{-- Pendapatan --}}
      <div class="slip-section-title text-primary" style="background:#f8f9ff;border-top:1px solid #eef2ff;">
        <i class="bi bi-cash-stack me-2"></i> Komponen Penerimaan
      </div>
      <table class="slip-table">
        <tbody>
          <tr>
            <td class="label-col">Gaji Pokok (Base Salary)</td>
            <td class="amount text-dark">Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="label-col">Total Pendapatan Tunjangan</td>
            <td class="amount text-success">+ Rp {{ number_format($penggajian->total_tunjangan, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="label-col">Kompensasi Lembur (Overtime)</td>
            <td class="amount text-success">+ Rp {{ number_format($penggajian->lembur, 0, ',', '.') }}</td>
          </tr>
          @php $totalBruto = $penggajian->gaji_pokok + $penggajian->total_tunjangan + $penggajian->lembur; @endphp
          <tr class="slip-total-row">
            <td style="color:#25396f">Total Penghasilan Kotor (Bruto)</td>
            <td class="amount text-primary">Rp {{ number_format($totalBruto, 0, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>

      {{-- Potongan --}}
      <div class="slip-section-title text-danger" style="background:#fff5f5;border-top:1px solid #ffeded;">
        <i class="bi bi-scissors me-2"></i> Komponen Potongan
      </div>
      <table class="slip-table">
        <tbody>
          @if($rincianPotongan['alpha_count'] > 0)
          <tr>
            <td class="label-col">Potongan Mangkir/Alpha ({{ $rincianPotongan['alpha_count'] }} Hari)</td>
            <td class="amount text-danger">- Rp {{ number_format($rincianPotongan['alpha_nominal'], 0, ',', '.') }}</td>
          </tr>
          @endif
          @if($rincianPotongan['telat_count'] > 0)
          <tr>
            <td class="label-col">Denda Keterlambatan ({{ $rincianPotongan['telat_count'] }} Kali)</td>
            <td class="amount text-danger">- Rp {{ number_format($rincianPotongan['telat_nominal'], 0, ',', '.') }}</td>
          </tr>
          @endif
          @foreach($rincianPotongan['lain_lain'] as $lain)
          <tr>
            <td class="label-col">{{ $lain['nama'] }}</td>
            <td class="amount text-danger">- Rp {{ number_format($lain['nominal'], 0, ',', '.') }}</td>
          </tr>
          @endforeach
          @if($rincianPotongan['alpha_count'] == 0 && $rincianPotongan['telat_count'] == 0 && count($rincianPotongan['lain_lain']) == 0)
          <tr>
            <td class="label-col">Potongan Administratif (Lain-lain)</td>
            <td class="amount text-danger">- Rp 0</td>
          </tr>
          @endif
          <tr>
            <td class="label-col">Pajak Penghasilan (PPh 21)</td>
            <td class="amount text-danger">- Rp {{ number_format($penggajian->pajak_pph21, 0, ',', '.') }}</td>
          </tr>
          @php 
            $totalPotongan = $rincianPotongan['alpha_nominal'] + $rincianPotongan['telat_nominal'] + $rincianPotongan['total_lain'] + $penggajian->pajak_pph21; 
          @endphp
          <tr class="slip-total-row">
            <td style="color:#dc3545">Total Seluruh Potongan</td>
            <td class="amount text-danger">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>

      {{-- Netto --}}
      <div class="slip-netto-bar">
        <div>
          <div style="font-size:0.9rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;opacity:0.9">Total Gaji Bersih (Netto)</div>
          <div style="font-size:0.75rem;opacity:0.8;margin-top:4px;">Disetujui untuk ditransfer ke rekening pegawai</div>
        </div>
        <div style="font-size:2rem;font-weight:900;letter-spacing:1px;text-shadow: 0 2px 4px rgba(0,0,0,0.1)">
          Rp {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}
        </div>
      </div>

      {{-- Footer --}}
      <div class="slip-footer d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold text-dark">Digital Signature Verified</div>
          <div>Dicetak secara mandiri oleh {{ $penggajian->pegawai->nama_pegawai }}</div>
          <div class="small opacity-75">Generated on: {{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
        </div>
        <div class="text-center no-print" style="opacity:0.2">
            <i class="bi bi-qr-code fs-1"></i>
            <div style="font-size:10px">SECURITY CODE: LG-{{ $penggajian->id_penggajian }}-{{ now()->year }}</div>
        </div>
        <div class="text-end" style="width: 150px;">
          <div style="font-size:0.75rem;margin-bottom:45px;">Ditetapkan Ole,</div>
          <div style="border-top:2px solid #25396f;padding-top:5px;font-weight:700;color:#25396f;font-size:0.85rem">MANAGEMENT</div>
        </div>
      </div>

    </div>
  </div>

  <div class="col-lg-3 col-md-12 no-print">
    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
      <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0 text-white">Opsi Slip Gaji</h5>
      </div>
      <div class="card-body py-4">
        <div class="text-center mb-4">
            <div class="avatar avatar-xl bg-primary-light mb-3">
                <i class="bi bi-file-earmark-pdf fs-1 text-primary"></i>
            </div>
            <p class="text-muted small">Simpan atau cetak slip gaji Anda untuk keperluan administrasi pribadi.</p>
        </div>
        
        <button onclick="window.print()" class="btn btn-primary w-100 mb-2 py-2 fw-bold shadow-sm">
          <i class="bi bi-printer-fill me-2"></i> Cetak / Save PDF
        </button>
        
        <a href="{{ route('students.payroll.index') }}" class="btn btn-outline-secondary w-100 py-2">
          <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
        </a>
      </div>
      <div class="card-footer bg-light border-0 small text-center text-muted">
        Jika ada ketidaksesuaian data, silakan hubungi bagian HRD.
      </div>
    </div>
  </div>
</div>

@endsection

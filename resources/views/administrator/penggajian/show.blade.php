@extends('layouts.app')

@section('title', 'Detail Slip Gaji')
@section('description', 'Detail Perhitungan Gaji Bulanan')

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

  .slip-header-bar {
    background: linear-gradient(135deg, #2c3e7a 0%, #3b5998 100%);
    color: #fff;
    padding: 20px 24px;
    border-radius: 8px 8px 0 0;
  }
  .slip-company-name { font-size: 1.25rem; font-weight: 700; letter-spacing: 0.5px; }
  .slip-company-sub { font-size: 0.8rem; opacity: 0.85; }
  .slip-title-bar {
    background: #f0f4ff;
    border-bottom: 2px solid #2c3e7a;
    padding: 12px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .slip-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 32px; padding: 20px 24px; }
  .slip-info-label { font-size: 0.7rem; text-transform: uppercase; color: #6c757d; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 2px; }
  .slip-info-value { font-size: 0.95rem; font-weight: 600; color: #212529; }
  .slip-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 10px 24px; margin: 0; }
  .slip-table { width: 100%; border-collapse: collapse; }
  .slip-table th, .slip-table td { padding: 10px 24px; font-size: 0.9rem; }
  .slip-table tbody tr { border-bottom: 1px solid #f0f0f0; }
  .slip-table tbody tr:last-child { border-bottom: none; }
  .slip-table .amount { text-align: right; font-weight: 600; white-space: nowrap; }
  .slip-table .label-col { color: #495057; }
  .slip-total-row { background: #f8f9fa; font-weight: 700; border-top: 2px solid #dee2e6 !important; }
  .slip-netto-bar {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: #fff;
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .slip-footer {
    padding: 16px 24px;
    background: #fafbfc;
    border-top: 1px solid #e9ecef;
    font-size: 0.78rem;
    color: #6c757d;
    border-radius: 0 0 8px 8px;
  }
  .slip-card { border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
</style>

<div class="row">
  <div class="col-lg-9 col-md-8">
    <div id="printable-slip" class="slip-card mb-4">

      {{-- Company Header --}}
      <div class="slip-header-bar">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="slip-company-name">PT LAGUNA GROUP</div>
            <div class="slip-company-sub">Sistem Penggajian & Kepegawaian</div>
          </div>
          <div class="text-end">
            <div style="font-size:0.8rem;opacity:0.8;">SLIP GAJI</div>
            <div style="font-size:1.1rem;font-weight:700;">{{ $penggajian->periode ?? '-' }}</div>
          </div>
        </div>
      </div>

      {{-- Status Bar --}}
      <div class="slip-title-bar">
        <div style="font-weight:600;color:#2c3e7a;">
          <i class="bi bi-file-earmark-text me-1"></i> Slip Gaji Bulanan — {{ $penggajian->periode ?? '-' }}
        </div>
        @if($penggajian->status == 'paid')
          <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>LUNAS</span>
        @else
          <span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-clock me-1"></i>MENUNGGU</span>
        @endif
      </div>

      {{-- Employee Info --}}
      <div class="slip-info-grid">
        <div>
          <div class="slip-info-label">Nama Pegawai</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->nama_pegawai ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">NIK</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->nik_pegawai ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">Jabatan</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->jabatan->nama_jabatan ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">Departemen</div>
          <div class="slip-info-value">{{ $penggajian->pegawai->departemen->nama_departemen ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">Periode Gaji</div>
          <div class="slip-info-value text-primary">{{ $penggajian->periode ?? '-' }}</div>
        </div>
        <div>
          <div class="slip-info-label">Tanggal Transfer</div>
          <div class="slip-info-value">
            @if($penggajian->tanggal_transfer)
              {{ \Carbon\Carbon::parse($penggajian->tanggal_transfer)->translatedFormat('d F Y') }}
            @else
              <span class="text-muted">Belum ditentukan</span>
            @endif
          </div>
        </div>
      </div>

      {{-- Pendapatan --}}
      <div class="slip-section-title text-success" style="background:#f0faf0;border-top:1px solid #e9ecef;">
        <i class="bi bi-plus-circle me-1"></i> PENDAPATAN
      </div>
      <table class="slip-table">
        <thead style="display:none;">
          <tr><th>Komponen</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
          <tr>
            <td class="label-col">Gaji Pokok</td>
            <td class="amount">Rp {{ number_format($penggajian->gaji_pokok ?? 0, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="label-col">Total Tunjangan</td>
            <td class="amount text-success">+ Rp {{ number_format($penggajian->total_tunjangan ?? 0, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="label-col">Upah Lembur</td>
            <td class="amount text-info">+ Rp {{ number_format($penggajian->lembur ?? 0, 0, ',', '.') }}</td>
          </tr>
          @php
            $totalBruto = ($penggajian->gaji_pokok ?? 0) + ($penggajian->total_tunjangan ?? 0) + ($penggajian->lembur ?? 0);
          @endphp
          <tr class="slip-total-row">
            <td>Total Pendapatan Bruto</td>
            <td class="amount">Rp {{ number_format($totalBruto, 0, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>

      {{-- Potongan --}}
      <div class="slip-section-title text-danger" style="background:#fef0f0;border-top:1px solid #e9ecef;">
        <i class="bi bi-dash-circle me-1"></i> POTONGAN
      </div>
      <table class="slip-table">
        <tbody>
          <tr>
            <td class="label-col">Total Potongan (BPJS, dll)</td>
            <td class="amount text-danger">- Rp {{ number_format($penggajian->total_potongan ?? 0, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="label-col">Pajak PPh 21</td>
            <td class="amount text-danger">- Rp {{ number_format($penggajian->pajak_pph21 ?? 0, 0, ',', '.') }}</td>
          </tr>
          @php
            $totalPotongan = ($penggajian->total_potongan ?? 0) + ($penggajian->pajak_pph21 ?? 0);
          @endphp
          <tr class="slip-total-row">
            <td>Total Potongan</td>
            <td class="amount text-danger">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>

      {{-- Gaji Bersih --}}
      <div class="slip-netto-bar">
        <div>
          <div style="font-size:0.85rem;opacity:0.85;text-transform:uppercase;letter-spacing:1px;">Gaji Bersih Diterima</div>
          @if($penggajian->pegawai->bank_pegawai || $penggajian->pegawai->no_rekening)
            <div style="font-size:0.75rem;opacity:0.7;margin-top:2px;">
              Transfer ke: {{ $penggajian->pegawai->bank_pegawai ?? '' }} {{ $penggajian->pegawai->no_rekening ?? '' }}
            </div>
          @endif
        </div>
        <div style="font-size:1.6rem;font-weight:800;letter-spacing:0.5px;">
          Rp {{ number_format($penggajian->gaji_bersih ?? 0, 0, ',', '.') }}
        </div>
      </div>

      {{-- Footer --}}
      <div class="slip-footer d-flex justify-content-between align-items-center">
        <div>
          <div>Dokumen ini digenerate secara otomatis oleh sistem.</div>
          <div>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>
        <div class="text-end">
          <div style="margin-bottom:40px;font-size:0.8rem;">Mengetahui,</div>
          <div style="border-top:1px solid #adb5bd;padding-top:4px;font-weight:600;">HRD / Petugas</div>
        </div>
      </div>

    </div>
  </div>

  {{-- Side Panel --}}
  <div class="col-lg-3 col-md-4 no-print">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">Aksi</h5>
      </div>
      <div class="card-body">
        <a href="{{ route('administrators.penggajian.index') }}" class="btn btn-outline-secondary w-100 mb-2">
          <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <button onclick="window.print()" class="btn btn-primary w-100">
          <i class="bi bi-printer"></i> Cetak Slip Gaji
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
      <div class="card-header">
        <h6 class="card-title mb-0 text-muted">Ringkasan</h6>
      </div>
      <div class="card-body small">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Pendapatan Bruto</span>
          <span class="fw-bold">Rp {{ number_format($totalBruto, 0, ',', '.') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Total Potongan</span>
          <span class="fw-bold text-danger">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
          <span class="fw-bold text-success">Gaji Bersih</span>
          <span class="fw-bold text-success">Rp {{ number_format($penggajian->gaji_bersih ?? 0, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
      <div class="card-header">
        <h6 class="card-title mb-0 text-muted">Info Sistem</h6>
      </div>
      <div class="card-body small text-muted">
        <p><i class="bi bi-calendar-month me-1 text-primary"></i><strong>Periode:</strong> Gaji dihitung per bulan penuh</p>
        <p><i class="bi bi-calendar-check me-1 text-success"></i><strong>Pembayaran:</strong> Setiap akhir bulan</p>
        <p class="mb-0"><i class="bi bi-calculator me-1 text-info"></i><strong>Komponen:</strong></p>
        <ul class="mb-0">
          <li>Gaji Pokok (1 bulan)</li>
          <li>+ Tunjangan tetap</li>
          <li>+ Upah lembur</li>
          <li>− Potongan (BPJS, dll)</li>
          <li>− Pajak PPh 21</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

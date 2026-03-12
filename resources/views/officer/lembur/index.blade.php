@extends('layouts.app')

@section('title', 'Data Lembur Tim Saya')
@section('description', 'Kelola Data Lembur')

@section('content')

@include('utilities.alert')

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
        <h4 class="card-title fw-bold mb-0">Daftar Lembur Tim Saya</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBeriLembur">
                <i class="bi bi-plus-circle me-1"></i> Beri Jatah Lembur
            </button>
            <span class="badge bg-light text-primary border border-primary px-3 py-2">{{ $lembur->count() }} Data</span>
        </div>
      </div>
      <div class="card-body">
        @if($lembur->count() > 0)
          {{-- Notifikasi jika ada lembur yang sedang berjalan --}}
          @php
            $sedangLembur = $lembur->filter(function($item) {
              return $item->status === 'pending' && $item->tanggal_lembur === now()->format('Y-m-d') && is_null($item->jam_selesai) === false;
            });
          @endphp

          @if($sedangLembur->count() > 0)
          <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
              <strong>{{ $sedangLembur->count() }} pegawai</strong> sedang dalam status lembur hari ini dan belum absen pulang.
              Data lembur di bawah diperbarui otomatis saat halaman dibuka.
            </div>
          </div>
          @endif

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nama Pegawai</th>
                  <th>Tanggal</th>
                  <th>Mulai Lembur</th>
                  <th>Selesai</th>
                  <th>Durasi (Jam)</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($lembur as $item)
                <tr>
                  <td class="fw-semibold">{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                  <td>{{ \Carbon\Carbon::parse($item->tanggal_lembur)->format('d/m/Y') }}</td>
                  <td>
                    <span class="badge bg-secondary">
                      <i class="bi bi-clock me-1"></i>{{ $item->jam_mulai ?? '-' }}
                    </span>
                  </td>
                  <td>
                    @if($item->jam_selesai)
                      <span class="badge bg-dark">{{ $item->jam_selesai }}</span>
                    @else
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-hourglass-split me-1"></i>Sedang Berjalan
                      </span>
                    @endif
                  </td>
                  <td>
                    @if($item->durasi)
                      <span class="badge bg-info text-dark">{{ number_format($item->durasi, 2) }} jam</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <small class="text-muted">{{ Str::limit($item->keterangan, 60) ?? '-' }}</small>
                  </td>
                  <td>
                    @if($item->status === 'approved')
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Telah Disetujui
                      </span>
                    @elseif($item->status === 'pending')
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock-history me-1"></i>Menunggu Persetujuan
                      </span>
                    @else
                      <span class="badge bg-secondary">{{ $item->status ?? '-' }}</span>
                    @endif
                  </td>
                  <td>
                    @if($item->status === 'pending' && $item->jam_selesai)
                      <form action="{{ route('officers.lembur.approve', $item->id_lembur) }}" method="POST" class="d-inline">
                        @csrf
                        @method('POST')
                        <button type="button" class="btn btn-sm btn-success btn-approve-lembur">
                          <i class="bi bi-check-lg"></i> Setujui
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="alert alert-light border mt-2 small text-muted">
            <i class="bi bi-info-circle me-1 text-primary"></i>
            Data lembur dengan status <strong>"Sedang Berjalan"</strong> adalah lembur yang terdeteksi otomatis
            dari pegawai yang belum absen pulang melewati jam jadwal. Data akan diperbarui setiap kali halaman dimuat.
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="bi bi-clock-history fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-1 fw-semibold">Belum ada data lembur untuk tim Anda.</p>
            <small>Data lembur akan muncul otomatis jika ada pegawai yang bekerja melewati jam jadwal pulang.</small>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@push('script')
<script>
    $(function() {
        $('.btn-approve-lembur').click(function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Setujui Lembur?',
                text: "Data lembur ini akan divalidasi dan masuk ke perhitungan gaji.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Setujui!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

{{-- Modal Beri Jatah Lembur --}}
<div class="modal fade" id="modalBeriLembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('officers.lembur.store') }}" method="POST" id="formBeriLembur">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-megaphone me-2"></i>Beri Jatah Lembur (Notifikasi)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Pilih Pegawai</label>
                            <select name="id_pegawai" class="form-select select2-basic" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id_pegawai }}">{{ $emp->nama_pegawai }} ({{ $emp->nik_pegawai }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lembur</label>
                            <input type="date" name="tanggal_lembur" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pesan / Keterangan Lembur</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Tolong siapkan laporan keuangan bulanan malam ini."></textarea>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 mt-3 mb-0 small">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Pemberian jatah lembur akan membuat notifikasi bahwa pegawai diizinkan lembur.
                        Jika pegawai lupa absen pulang, jam pulang otomatis akan diatur ke <strong> pukul 21:00</strong>.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-send me-1"></i>Kirim Notifikasi Lembur
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

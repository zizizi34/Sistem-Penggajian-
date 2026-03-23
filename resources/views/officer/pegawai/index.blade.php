@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('description', 'Daftar Pegawai Departemen Anda')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Daftar Pegawai</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPegawai">
          <i class="bi bi-plus"></i> Tambah Pegawai
        </button>
      </div>
      <div class="card-body">
        @if($pegawai->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Jabatan</th>
                  <th>Gaji Pokok</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pegawai as $item)
                <tr>
                  <td>{{ $item->nik_pegawai ?? '-' }}</td>
                  <td class="fw-semibold">
                    <a href="{{ route('officers.pegawai.show', $item->id_pegawai) }}" class="text-dark text-decoration-none">
                      {{ $item->nama_pegawai ?? '-' }} <i class="bi bi-box-arrow-up-right ms-1 text-muted small"></i>
                    </a>
                  </td>
                  <td>{{ $item->email_pegawai ?? '-' }}</td>
                  <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge bg-{{ $item->status_pegawai == 'aktif' ? 'success' : 'danger' }}">
                      {{ ucfirst($item->status_pegawai ?? 'aktif') }}
                    </span>
                  </td>
                  <td>
                    <button type="button"
                       class="btn btn-sm btn-warning btn-edit-pegawai"
                       data-bs-toggle="modal"
                       data-bs-target="#modalEditPegawai"
                       data-id="{{ $item->id_pegawai }}"
                       data-nik="{{ $item->nik_pegawai }}"
                       data-nama="{{ $item->nama_pegawai }}"
                       data-jk="{{ $item->jenis_kelamin }}"
                       data-tgl_lahir="{{ $item->tanggal_lahir }}"
                       data-alamat="{{ $item->alamat }}"
                       data-no_hp="{{ $item->no_hp }}"
                       data-email="{{ $item->email_pegawai }}"
                       data-id_jabatan="{{ $item->id_jabatan }}"
                       data-status="{{ $item->status_pegawai }}"
                       data-tgl_masuk="{{ $item->tgl_masuk }}"
                       data-gaji="{{ (int)$item->gaji_pokok }}"
                       data-id_ptkp="{{ $item->id_ptkp_status }}"
                       data-npwp="{{ $item->npwp }}"
                       data-bank="{{ $item->bank_pegawai }}"
                       data-rekening="{{ $item->no_rekening }}"
                       data-url="{{ route('officers.pegawai.update', $item->id_pegawai) }}"
                       title="Edit Pegawai">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-danger"
                            data-delete-action="{{ route('officers.pegawai.destroy', $item->id_pegawai) }}"
                            data-delete-item="{{ $item->nama_pegawai }}"
                            data-delete-category="pegawai"
                            title="Hapus Pegawai">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
            <p class="mb-1 fw-semibold">Belum ada data pegawai di departemen Anda.</p>
            <small>Klik tombol <strong>Tambah Pegawai</strong> untuk menambahkan data.</small>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('components.delete-confirmation-modal')
@include('officer.pegawai.modal.create')
@include('officer.pegawai.modal.edit')

@push('script')
<script>
    $(document).ready(function() {
        $('.btn-edit-pegawai').click(function() {
            const data = $(this).data();
            const form = $('#formEditPegawai');
            
            form.attr('action', data.url);
            $('#edit_nik_pegawai').val(data.nik);
            $('#edit_nama_pegawai').val(data.nama);
            $('#edit_jenis_kelamin').val(data.jk);
            $('#edit_tanggal_lahir').val(data.tgl_lahir);
            $('#edit_alamat').val(data.alamat);
            $('#edit_no_hp').val(data.no_hp);
            $('#edit_email_pegawai').val(data.email);
            $('#edit_id_jabatan').val(data.id_jabatan);
            $('#edit_status_pegawai').val(data.status);
            $('#edit_tgl_masuk').val(data.tgl_masuk);
            $('#edit_gaji_pokok').val(data.gaji);
            $('#edit_id_ptkp_status').val(data.id_ptkp);
            $('#edit_npwp').val(data.npwp);
            $('#edit_bank_pegawai').val(data.bank);
            $('#edit_no_rekening').val(data.rekening);
            
            $('#modalEditPegawai').modal('show');
        });

        // Re-open modal with errors
        @if($errors->any())
            @if(old('_method') == 'PUT')
                $('#modalEditPegawai').modal('show');
            @else
                $('#modalTambahPegawai').modal('show');
            @endif
        @endif
    });
</script>
@endpush
@endsection

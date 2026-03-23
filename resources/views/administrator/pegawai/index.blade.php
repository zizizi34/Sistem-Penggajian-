@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('description', 'Kelola Data Pegawai')

@section('content')
<div class="row">
  <div class="col-12">
    @include('utilities.alert')
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Daftar Pegawai</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPegawai">
          <i class="bi bi-person-plus-fill me-1"></i> Tambah Pegawai
        </button>
      </div>
      <div class="card-body">
        @if($pegawai->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Departemen</th>
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
                  <td>{{ $item->nama_pegawai ?? '-' }}</td>
                  <td>{{ $item->departemen->nama_departemen ?? '-' }}</td>
                  <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                  <td>Rp {{ number_format($item->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                  <td>
                    <span class="badge bg-{{ $item->status_pegawai == 'aktif' ? 'success' : 'danger' }}">
                      {{ ucfirst($item->status_pegawai ?? 'aktif') }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('administrators.pegawai.show', $item->id_pegawai) }}" class="btn btn-info" title="Lihat Detail">
                        <i class="bi bi-eye"></i>
                      </a>
                      <button type="button" class="btn btn-warning btn-edit-pegawai" 
                        data-bs-toggle="modal" data-bs-target="#modalEditPegawai"
                        data-id="{{ $item->id_pegawai }}"
                        data-nik="{{ $item->nik_pegawai }}"
                        data-nama="{{ $item->nama_pegawai }}"
                        data-id_dept="{{ $item->id_departemen }}"
                        data-id_jab="{{ $item->id_jabatan }}"
                        data-jk="{{ $item->jenis_kelamin }}"
                        data-tgl_lahir="{{ $item->tanggal_lahir }}"
                        data-email="{{ $item->email_pegawai }}"
                        data-hp="{{ $item->no_hp }}"
                        data-tgl_masuk="{{ $item->tgl_masuk }}"
                        data-gaji="{{ (int)$item->gaji_pokok }}"
                        data-status="{{ $item->status_pegawai }}"
                        data-alamat="{{ $item->alamat }}"
                        data-id_ptkp="{{ $item->id_ptkp_status }}"
                        data-url="{{ route('administrators.pegawai.update', $item->id_pegawai) }}"
                        title="Edit Data">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" class="btn btn-danger btn-delete" 
                        data-delete-action="{{ route('administrators.pegawai.destroy', $item->id_pegawai) }}"
                        data-delete-item="{{ $item->nama_pegawai }}"
                        data-delete-category="Pegawai"
                         title="Hapus Data">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-center text-muted">Belum ada data pegawai</p>
        @endif
      </div>
    </div>
  </div>
</div>

@include('administrator.pegawai.modal.create')
@include('administrator.pegawai.modal.edit')
@include('components.delete-confirmation-modal')

@push('script')
<script>
    $(document).ready(function() {
        $('.btn-edit-pegawai').click(function() {
            const data = $(this).data();
            const form = $('#formEditPegawai');
            
            form.attr('action', data.url);
            $('#edit_nik_pegawai').val(data.nik);
            $('#edit_nama_pegawai').val(data.nama);
            $('#edit_id_departemen').val(data.id_dept);
            $('#edit_id_jabatan').val(data.id_jab);
            $('#edit_jenis_kelamin').val(data.jk);
            $('#edit_tanggal_lahir').val(data.tgl_lahir);
            $('#edit_email_pegawai').val(data.email);
            $('#edit_no_hp').val(data.hp);
            $('#edit_tgl_masuk').val(data.tgl_masuk);
            $('#edit_gaji_pokok').val(data.gaji);
            $('#edit_status_pegawai').val(data.status);
            $('#edit_alamat').val(data.alamat);
            $('#edit_id_ptkp_status').val(data.id_ptkp);
            
            $('#modalEditPegawai').modal('show');
        });

        // Error handling: auto-open modal if validation fails
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

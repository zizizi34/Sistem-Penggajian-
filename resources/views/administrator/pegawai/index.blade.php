@extends('layouts.administrator.app')

@section('title', 'Data Pegawai')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Pegawai</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('administrators.pegawai.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Tambah Pegawai
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Daftar Pegawai</h5>
                    </div>
                    <div class="card-body">
                        @if ($pegawai->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Departemen</th>
                                            <th>Jabatan</th>
                                            <th>Gaji Pokok</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pegawai as $item)
                                            <tr>
                                                <td>{{ $item->id_pegawai }}</td>
                                                <td>{{ $item->nama_pegawai }}</td>
                                                <td>{{ $item->email_pegawai ?? '-' }}</td>
                                                <td>{{ $item->departemen->nama_departemen ?? '-' }}</td>
                                                <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                                                <td>Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('administrators.pegawai.edit', $item->id_pegawai) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrators.pegawai.destroy', $item->id_pegawai) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                            <i class="fe fe-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Belum ada data pegawai. <a href="{{ route('administrators.pegawai.create') }}">Tambah data pegawai</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

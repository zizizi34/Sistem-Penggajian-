@extends('layouts.administrator.app')

@section('title', 'Data Jabatan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Jabatan</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('administrator.jabatan.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Tambah Jabatan
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
                        <h5 class="card-title">Daftar Jabatan</h5>
                    </div>
                    <div class="card-body">
                        @if ($jabatan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Jabatan</th>
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jabatan as $item)
                                            <tr>
                                                <td>{{ $item->id_jabatan }}</td>
                                                <td>{{ $item->nama_jabatan }}</td>
                                                <td>{{ $item->deskripsi_jabatan ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('administrator.jabatan.edit', $item->id_jabatan) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrator.jabatan.destroy', $item->id_jabatan) }}" method="POST" style="display:inline;">
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
                                Belum ada data jabatan. <a href="{{ route('administrator.jabatan.create') }}">Tambah data jabatan</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

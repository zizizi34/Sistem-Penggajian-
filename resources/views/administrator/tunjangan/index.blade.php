@extends('layouts.administrator.app')

@section('title', 'Data Tunjangan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Tunjangan</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('administrator.tunjangan.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Tambah Tunjangan
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
                        <h5 class="card-title">Daftar Tunjangan</h5>
                    </div>
                    <div class="card-body">
                        @if ($tunjangan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Tunjangan</th>
                                            <th>Nominal</th>
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tunjangan as $item)
                                            <tr>
                                                <td>{{ $item->id_tunjangan }}</td>
                                                <td>{{ $item->nama_tunjangan }}</td>
                                                <td>Rp {{ number_format($item->nominal_tunjangan, 0, ',', '.') }}</td>
                                                <td>{{ $item->deskripsi_tunjangan ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('administrator.tunjangan.edit', $item->id_tunjangan) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrator.tunjangan.destroy', $item->id_tunjangan) }}" method="POST" style="display:inline;">
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
                                Belum ada data tunjangan. <a href="{{ route('administrator.tunjangan.create') }}">Tambah data tunjangan</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

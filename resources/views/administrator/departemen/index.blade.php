@extends('layouts.administrator.app')

@section('title', 'Data Departemen')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Departemen</h1>
            </div>
            <div class="col-lg-6 text-end">
             <a href="{{ route('administrators.departemen.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Tambah Departemen
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
                        <h5 class="card-title">Daftar Departemen</h5>
                    </div>
                    <div class="card-body">
                        @if ($departemen->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Departemen</th>
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departemen as $item)
                                            <tr>
                                                <td>{{ $item->id_departemen }}</td>
                                                <td>{{ $item->nama_departemen }}</td>
                                                <td>{{ $item->deskripsi_departemen ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('administrators.departemen.edit', $item->id_departemen) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrators.departemen.destroy', $item->id_departemen) }}" method="POST" style="display:inline;">
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
                                Belum ada data departemen. <a href="{{ route('administrators.departemen.create') }}">Tambah data departemen</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.administrator.app')

@section('title', 'Data Status PTKP')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Status PTKP</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('administrator.ptkp_status.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Tambah PTKP
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
                        <h5 class="card-title">Daftar Status PTKP</h5>
                    </div>
                    <div class="card-body">
                        @if ($ptkp->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kode PTKP</th>
                                            <th>Deskripsi</th>
                                            <th>PTKP (Rp)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ptkp as $item)
                                            <tr>
                                                <td>{{ $item->id_ptkp_status }}</td>
                                                <td>{{ $item->kode_ptkp }}</td>
                                                <td>{{ $item->deskripsi_ptkp ?? '-' }}</td>
                                                <td>Rp {{ number_format($item->nilai_ptkp, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('administrator.ptkp_status.edit', $item->id_ptkp_status) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrator.ptkp_status.destroy', $item->id_ptkp_status) }}" method="POST" style="display:inline;">
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
                                Belum ada data PTKP. <a href="{{ route('administrator.ptkp_status.create') }}">Tambah data PTKP</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

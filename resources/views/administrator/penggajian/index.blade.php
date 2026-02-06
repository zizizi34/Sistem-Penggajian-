@extends('layouts.administrator.app')

@section('title', 'Data Penggajian')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Penggajian</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('administrator.penggajian.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Hitung Gaji
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
                        <h5 class="card-title">Daftar Penggajian</h5>
                    </div>
                    <div class="card-body">
                        @if ($penggajian->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Pegawai</th>
                                            <th>Periode</th>
                                            <th>Gaji Bersih</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penggajian as $item)
                                            <tr>
                                                <td>{{ $item->id_penggajian }}</td>
                                                <td>{{ $item->pegawai->nama_pegawai ?? '-' }}</td>
                                                <td>{{ $item->periode_penggajian ?? '-' }}</td>
                                                <td>Rp {{ number_format($item->gaji_bersih ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $item->status_penggajian == 'approved' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($item->status_penggajian) ?? 'Draft' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('administrator.penggajian.show', $item->id_penggajian) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    <a href="{{ route('administrator.penggajian.edit', $item->id_penggajian) }}" class="btn btn-sm btn-warning">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrator.penggajian.destroy', $item->id_penggajian) }}" method="POST" style="display:inline;">
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
                                Belum ada data penggajian. <a href="{{ route('administrator.penggajian.create') }}">Hitung gaji pegawai</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

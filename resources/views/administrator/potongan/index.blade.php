@extends('layouts.administrator.app')

@section('title', 'Data Potongan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">Data Potongan</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('administrators.potongan.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus me-2"></i>Tambah Potongan
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
                        <h5 class="card-title">Daftar Potongan</h5>
                    </div>
                    <div class="card-body">
                        @if ($potongan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-md">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Potongan</th>
                                            <th>Nominal</th>
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($potongan as $item)
                                            <tr>
                                                <td>{{ $item->id_potongan }}</td>
                                                <td>{{ $item->nama_potongan }}</td>
                                                <td>Rp {{ number_format($item->nominal_potongan, 0, ',', '.') }}</td>
                                                <td>{{ $item->deskripsi_potongan ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('administrators.potongan.edit', $item->id_potongan) }}" class="btn btn-sm btn-info">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administrators.potongan.destroy', $item->id_potongan) }}" method="POST" style="display:inline;">
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
                                Belum ada data potongan. <a href="{{ route('administrators.potongan.create') }}">Tambah data potongan</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

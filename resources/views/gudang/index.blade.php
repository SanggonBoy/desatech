@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Data Gudang</h5>
                <a href="{{ route('gudang.create') }}" class="btn btn-primary">
                    <i class="feather-plus"></i> Tambah Gudang
                </a>
            </div>
            <div class="card-body custom-card-action p-0">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="table">
                        <thead>
                            <tr class="border-b">
                                <th>No</th>
                                <th>Kode Gudang</th>
                                <th>Nama Gudang</th>
                                <th>PIC</th>
                                <th>No. Telp</th>
                                <th>Total Item</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gudangs as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $item->kode_gudang }}</strong></td>
                                    <td>{{ $item->nama_gudang }}</td>
                                    <td>{{ $item->pic ?? '-' }}</td>
                                    <td>{{ $item->no_telp ?? '-' }}</td>
                                    <td><span class="badge bg-info">{{ $item->stoks_count }} Item</span></td>
                                    <td>
                                        @if ($item->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('gudang.show', $item->id) }}" class="btn btn-sm btn-info">
                                                <i class="feather-eye text-white"></i>
                                            </a>
                                            <a href="{{ route('gudang.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="feather-edit text-white"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $item->id }}" data-nama="{{ $item->nama_gudang }}">
                                                <i class="feather-trash-2 text-white"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('custom/js/gudang.js') }}"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        </script>
    @endif
@endsection

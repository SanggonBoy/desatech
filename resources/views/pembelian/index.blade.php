@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Data Transaksi Pembelian</h5>
                <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
                    <i class="feather-plus"></i> Tambah Pembelian
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
                                <th>No. Pembelian</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelians as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $item->no_pembelian }}</strong></td>
                                    <td>{{ $item->tanggal_pembelian->format('d/m/Y') }}</td>
                                    <td>{{ $item->supplier->nama_supplier }}</td>
                                    <td>Rp {{ number_format($item->total_pembelian, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($item->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @else
                                            <span class="badge bg-success">Approved</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-sm btn-info">
                                                <i class="feather-eye text-white"></i>
                                            </a>
                                            @if ($item->status == 'draft')
                                                <a href="{{ route('pembelian.edit', $item->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="feather-edit text-white"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $item->id }}" data-no="{{ $item->no_pembelian }}">
                                                    <i class="feather-trash-2 text-white"></i>
                                                </button>
                                            @endif
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
    <script src="{{ asset('custom/js/pembelian-index.js') }}"></script>
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

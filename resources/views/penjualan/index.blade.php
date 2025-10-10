@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Data Transaksi Penjualan</h5>
                <a href="{{ route('penjualan.create') }}" class="btn btn-primary">
                    <i class="feather-plus"></i> Tambah Penjualan
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
                                <th>No. Penjualan</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Gudang</th>
                                <th class="text-end">Total Penjualan</th>
                                <th class="text-end">Total HPP</th>
                                <th class="text-end">Profit</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualans as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $item->no_penjualan }}</strong></td>
                                    <td>{{ $item->tanggal_penjualan->format('d/m/Y') }}</td>
                                    <td>{{ $item->pelanggan->nama_pelanggan }}</td>
                                    <td>{{ $item->gudang->nama_gudang }}</td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @if ($item->status == 'completed')
                                            Rp {{ number_format($item->total_hpp, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($item->status == 'completed')
                                            <span class="badge bg-{{ $item->profit >= 0 ? 'success' : 'danger' }} fs-6">
                                                Rp {{ number_format($item->profit, 0, ',', '.') }}
                                                <small>({{ number_format($item->profit_percentage, 1) }}%)</small>
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @else
                                            <span class="badge bg-success">Completed</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('penjualan.show', $item->id) }}" class="btn btn-sm btn-info"
                                                title="Detail">
                                                <i class="feather-eye text-white"></i>
                                            </a>
                                            @if ($item->status == 'draft')
                                                <a href="{{ route('penjualan.edit', $item->id) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="feather-edit text-white"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $item->id }}" data-no="{{ $item->no_penjualan }}"
                                                    title="Hapus">
                                                    <i class="feather-trash-2 text-white"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($penjualans->where('status', 'completed')->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">TOTAL:</th>
                                    <th class="text-end">
                                        Rp
                                        {{ number_format($penjualans->where('status', 'completed')->sum('total_penjualan'), 0, ',', '.') }}
                                    </th>
                                    <th class="text-end">
                                        Rp
                                        {{ number_format($penjualans->where('status', 'completed')->sum('total_hpp'), 0, ',', '.') }}
                                    </th>
                                    <th class="text-end">
                                        <span class="badge bg-success fs-6">
                                            Rp
                                            {{ number_format($penjualans->where('status', 'completed')->sum('profit'), 0, ',', '.') }}
                                        </span>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        @endif
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
    <script src="{{ asset('custom/js/penjualan-index.js') }}"></script>
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

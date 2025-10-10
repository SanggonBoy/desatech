@extends('layout.main')

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Detail Pelanggan</h5>
                <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Kode Pelanggan</th>
                                <td>: <strong>{{ $pelanggan->kode_pelanggan }}</strong></td>
                            </tr>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <td>: {{ $pelanggan->nama_pelanggan }}</td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td>: {{ $pelanggan->no_telp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>: {{ $pelanggan->email ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Alamat</th>
                                <td>: {{ $pelanggan->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:
                                    @if ($pelanggan->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Total Transaksi</th>
                                <td>: <strong>{{ $pelanggan->penjualans->count() }} Penjualan</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($pelanggan->penjualans->count() > 0)
                    <hr>
                    <h5 class="mb-3">Riwayat Transaksi Penjualan (10 Terakhir)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>No. Penjualan</th>
                                    <th>Tanggal</th>
                                    <th>Gudang</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Profit</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelanggan->penjualans as $penjualan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $penjualan->no_penjualan }}</strong></td>
                                        <td>{{ $penjualan->tanggal_penjualan->format('d/m/Y') }}</td>
                                        <td>{{ $penjualan->gudang->nama_gudang }}</td>
                                        <td class="text-end">Rp
                                            {{ number_format($penjualan->total_penjualan, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            @if ($penjualan->status == 'completed')
                                                <span class="badge bg-success">
                                                    Rp {{ number_format($penjualan->profit, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($penjualan->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @else
                                                <span class="badge bg-success">Completed</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('penjualan.show', $penjualan->id) }}"
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="feather-eye text-white"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="feather-info"></i> Pelanggan ini belum memiliki transaksi penjualan.
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="btn btn-warning">
                        <i class="feather-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

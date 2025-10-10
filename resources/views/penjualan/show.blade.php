@extends('layout.main')

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Detail Transaksi Penjualan</h5>
                <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">No. Penjualan</th>
                                <td>: <strong>{{ $penjualan->no_penjualan }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>: {{ $penjualan->tanggal_penjualan->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Pelanggan</th>
                                <td>: {{ $penjualan->pelanggan->nama_pelanggan }}</td>
                            </tr>
                            <tr>
                                <th>Kode Pelanggan</th>
                                <td>: {{ $penjualan->pelanggan->kode_pelanggan }}</td>
                            </tr>
                            <tr>
                                <th>Gudang</th>
                                <td>: {{ $penjualan->gudang->nama_gudang }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Status</th>
                                <td>:
                                    @if ($penjualan->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Total Penjualan</th>
                                <td>: <strong class="text-primary">Rp
                                        {{ number_format($penjualan->total_penjualan, 0, ',', '.') }}</strong></td>
                            </tr>
                            @if ($penjualan->status == 'completed')
                                <tr>
                                    <th>Total HPP</th>
                                    <td>: <strong class="text-warning">Rp
                                            {{ number_format($penjualan->total_hpp, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Profit</th>
                                    <td>: <strong class="text-success">Rp
                                            {{ number_format($penjualan->profit, 0, ',', '.') }}
                                            <small>({{ number_format($penjualan->profit_percentage, 1) }}%)</small>
                                        </strong></td>
                                </tr>
                            @endif
                            <tr>
                                <th>Keterangan</th>
                                <td>: {{ $penjualan->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Detail Barang</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga Jual Satuan</th>
                                <th class="text-end">Subtotal Jual</th>
                                @if ($penjualan->status == 'completed')
                                    <th class="text-end">HPP Satuan</th>
                                    <th class="text-end">Subtotal HPP</th>
                                    <th class="text-end">Profit Item</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->details as $detail)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $detail->barang->kode_barang }}</td>
                                    <td>{{ $detail->barang->nama_barang }}</td>
                                    <td>{{ strtoupper($detail->barang->satuan_barang) }}</td>
                                    <td class="text-end">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->harga_jual_satuan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">Rp {{ number_format($detail->subtotal_jual, 0, ',', '.') }}</td>
                                    @if ($penjualan->status == 'completed')
                                        <td class="text-end">Rp {{ number_format($detail->hpp_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->subtotal_hpp, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $detail->profit_item >= 0 ? 'success' : 'danger' }}">
                                                Rp {{ number_format($detail->profit_item, 0, ',', '.') }}
                                                <small>({{ number_format($detail->profit_percentage, 1) }}%)</small>
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="{{ $penjualan->status == 'completed' ? '6' : '6' }}" class="text-end">Total
                                </th>
                                <th class="text-end">Rp {{ number_format($penjualan->total_penjualan, 0, ',', '.') }}</th>
                                @if ($penjualan->status == 'completed')
                                    <th class="text-end"></th>
                                    <th class="text-end">Rp {{ number_format($penjualan->total_hpp, 0, ',', '.') }}</th>
                                    <th class="text-end">
                                        <span class="badge bg-success fs-6">
                                            Rp {{ number_format($penjualan->profit, 0, ',', '.') }}
                                        </span>
                                    </th>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($penjualan->status == 'completed')
                    <div class="alert alert-info mt-3">
                        <i class="feather-info"></i>
                        <strong>Informasi FIFO:</strong>
                        HPP (Harga Pokok Penjualan) dihitung menggunakan metode FIFO (First In First Out).
                        Stok yang masuk lebih dulu akan keluar lebih dulu dengan harga belinya masing-masing.
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
                    @if ($penjualan->status == 'draft')
                        <a href="{{ route('penjualan.edit', $penjualan->id) }}" class="btn btn-warning">
                            <i class="feather-edit"></i> Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

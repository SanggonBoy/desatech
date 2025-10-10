@extends('layout.main')

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Detail Transaksi Pembelian</h5>
                <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">No. Pembelian</th>
                                <td>: <strong>{{ $pembelian->no_pembelian }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>: {{ $pembelian->tanggal_pembelian->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>: {{ $pembelian->supplier->nama_supplier }}</td>
                            </tr>
                            <tr>
                                <th>Kode Supplier</th>
                                <td>: {{ $pembelian->supplier->kode_supplier }}</td>
                            </tr>
                            <tr>
                                <th>Gudang</th>
                                <td>: {{ $pembelian->gudang ? $pembelian->gudang->nama_gudang : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Status</th>
                                <td>:
                                    @if ($pembelian->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-success">Approved</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Total Pembelian</th>
                                <td>: <strong class="text-primary">Rp
                                        {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>: {{ $pembelian->keterangan ?? '-' }}</td>
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
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelian->details as $detail)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $detail->barang->kode_barang }}</td>
                                    <td>{{ $detail->barang->nama_barang }}</td>
                                    <td>{{ strtoupper($detail->barang->satuan_barang) }}</td>
                                    <td class="text-end">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">Total</th>
                                <th class="text-end">Rp {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Kembali</a>
                    @if ($pembelian->status == 'draft')
                        <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="btn btn-warning">
                            <i class="feather-edit"></i> Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

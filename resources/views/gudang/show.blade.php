@extends('layout.main')

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Detail Gudang</h5>
                <a href="{{ route('gudang.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Kode Gudang</th>
                                <td>: <strong>{{ $gudang->kode_gudang }}</strong></td>
                            </tr>
                            <tr>
                                <th>Nama Gudang</th>
                                <td>: {{ $gudang->nama_gudang }}</td>
                            </tr>
                            <tr>
                                <th>PIC</th>
                                <td>: {{ $gudang->pic ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">No. Telepon</th>
                                <td>: {{ $gudang->no_telp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>: {{ $gudang->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:
                                    @if ($gudang->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Stok Barang di Gudang (Per Batch - FIFO System)</h5>
                <div class="alert alert-info">
                    <i class="feather-info"></i>
                    <strong>Sistem FIFO:</strong> Stok dikelola per batch (First In First Out). Batch yang masuk lebih dulu
                    akan keluar lebih dulu saat penjualan.
                </div>

                @if ($stokGrouped->count() > 0)
                    @foreach ($stokGrouped as $item)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-0">
                                            <strong>{{ $item['barang']->kode_barang }}</strong> -
                                            {{ $item['barang']->nama_barang }}
                                        </h6>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-secondary">{{ $item['jumlah_batch'] }} Batch</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="30">#</th>
                                                <th>Batch Number</th>
                                                <th>Tgl Masuk</th>
                                                <th>No. Pembelian</th>
                                                <th class="text-end">Masuk</th>
                                                <th class="text-end">Keluar</th>
                                                <th class="text-end">Sisa</th>
                                                <th class="text-end">HPP/Unit</th>
                                                <th class="text-end">Nilai Stok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item['batches'] as $batch)
                                                <tr class="{{ $batch->sisa_stok == 0 ? 'table-secondary' : '' }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <small class="font-monospace">{{ $batch->batch_number }}</small>
                                                    </td>
                                                    <td>{{ $batch->tanggal_masuk->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if ($batch->pembelian)
                                                            <a href="{{ route('pembelian.show', $batch->pembelian_id) }}"
                                                                class="text-primary">
                                                                {{ $batch->pembelian->no_pembelian }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        {{ number_format($batch->jumlah_masuk, 0, ',', '.') }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($batch->jumlah_keluar, 0, ',', '.') }}</td>
                                                    <td class="text-end">
                                                        <strong>{{ number_format($batch->sisa_stok, 0, ',', '.') }}</strong>
                                                    </td>
                                                    <td class="text-end">
                                                        Rp {{ number_format($batch->harga_beli_satuan, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>Rp
                                                            {{ number_format($batch->total_nilai_stok, 0, ',', '.') }}</strong>
                                                    </td>
                                                    <td>
                                                        @if ($batch->sisa_stok > 0)
                                                            <span class="badge bg-success">Aktif</span>
                                                        @else
                                                            <span class="badge bg-secondary">Habis</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4" class="text-end">TOTAL:</th>
                                                <th class="text-end">
                                                    {{ number_format($item['batches']->sum('jumlah_masuk'), 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">
                                                    {{ number_format($item['batches']->sum('jumlah_keluar'), 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">
                                                    {{ number_format($item['batches']->sum('sisa_stok'), 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">-</th>
                                                <th class="text-end">
                                                    <strong>Rp
                                                        {{ number_format($item['batches']->sum('total_nilai_stok'), 0, ',', '.') }}</strong>
                                                </th>
                                                <th>-</th>
                                            </tr>
                                            <tr>
                                                <th colspan="7" class="text-end">HPP Rata-rata:</th>
                                                <th colspan="3" class="text-end">
                                                    @php
                                                        $hppRataRata =
                                                            $item['total_stok'] > 0
                                                                ? $item['batches']->sum('total_nilai_stok') /
                                                                    $item['total_stok']
                                                                : 0;
                                                    @endphp
                                                    <span class="badge bg-info fs-6">
                                                        Rp {{ number_format($hppRataRata, 0, ',', '.') }}/unit
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <i class="feather-alert-triangle"></i>
                        Belum ada stok barang di gudang ini
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('gudang.edit', $gudang->id) }}" class="btn btn-warning">
                        <i class="feather-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layout.main')

@section('css')
    <style>
        .table-jurnal {
            font-size: 0.9rem;
        }

        .table-jurnal th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .filter-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Jurnal Umum</h5>
                <div class="card-header-action">
                    <span class="badge bg-info">Otomatis dari Transaksi</span>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filter -->
                <div class="filter-card">
                    <form method="GET" action="{{ route('jurnal-umum.index') }}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label class="form-label">Pilih Periode</label>
                                <input type="month" name="periode" class="form-control" value="{{ request('periode') }}"
                                    required>
                                <small class="text-muted">Contoh: Oktober 2025</small>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="feather-filter"></i> Tampilkan
                                    </button>
                                    <a href="{{ route('jurnal-umum.index') }}" class="btn btn-secondary flex-fill">
                                        <i class="feather-refresh-cw"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Periode Info -->
                @if (request('periode'))
                    @php
                        $periode = request('periode'); // Format: YYYY-MM
                        $date = \Carbon\Carbon::createFromFormat('Y-m', $periode);
                        $namaBulan = $date->locale('id')->translatedFormat('F Y'); // Contoh: Oktober 2025
                    @endphp
                    <div class="alert alert-success mb-3">
                        <strong><i class="feather-calendar"></i> Menampilkan data periode: {{ $namaBulan }}</strong>
                    </div>
                @endif

                <!-- Tabel Jurnal Umum -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-jurnal">
                        <thead>
                            <tr>
                                <th width="10%">Tanggal</th>
                                <th width="15%">No. Referensi</th>
                                <th>Keterangan</th>
                                <th width="12%">Kode Akun</th>
                                <th width="15%">Nama Akun</th>
                                <th width="12%" class="text-end">Debit (Rp)</th>
                                <th width="12%" class="text-end">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDebit = 0;
                                $totalKredit = 0;
                                $currentRef = null;
                            @endphp

                            @forelse ($jurnals as $jurnal)
                                @php
                                    $totalDebit += $jurnal->debit;
                                    $totalKredit += $jurnal->kredit;

                                    // Cek apakah ini referensi baru
                                    $isNewRef = $currentRef !== $jurnal->no_referensi;
                                    $currentRef = $jurnal->no_referensi;
                                @endphp

                                @if ($isNewRef && $loop->index > 0)
                                    <tr style="height: 10px; background-color: #fff;">
                                        <td colspan="7"></td>
                                    </tr>
                                @endif

                                <tr>
                                    <td>{{ $jurnal->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $jurnal->no_referensi }}</span>
                                    </td>
                                    <td>{{ $jurnal->keterangan }}</td>
                                    <td class="text-center">
                                        <strong>{{ $jurnal->coa->kode_akun }}</strong>
                                    </td>
                                    <td>{{ $jurnal->coa->nama_akun }}</td>
                                    <td class="text-end">
                                        @if ($jurnal->debit > 0)
                                            <strong>{{ number_format($jurnal->debit, 0, ',', '.') }}</strong>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($jurnal->kredit > 0)
                                            <strong>{{ number_format($jurnal->kredit, 0, ',', '.') }}</strong>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        @if (request('periode'))
                                            <em>Tidak ada transaksi pada periode ini</em>
                                        @else
                                            <em>Silakan pilih periode untuk menampilkan data jurnal umum</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse

                            @if ($jurnals->count() > 0)
                                <tr class="total-row">
                                    <td colspan="5" class="text-end">
                                        <strong>TOTAL</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($totalKredit, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        @if ($totalDebit == $totalKredit)
                                            <span class="badge bg-success">
                                                <i class="feather-check-circle"></i> Balance (Debit = Kredit)
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="feather-alert-circle"></i> Not Balance (Selisih:
                                                {{ number_format(abs($totalDebit - $totalKredit), 0, ',', '.') }})
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $jurnals->firstItem() ?? 0 }} - {{ $jurnals->lastItem() ?? 0 }} dari
                        {{ $jurnals->total() }} data
                    </div>
                    <div>
                        {{ $jurnals->appends(request()->query())->links() }}
                    </div>
                </div>

                <!-- Info -->
                <div class="alert alert-info mt-3">
                    <strong><i class="feather-info"></i> Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Jurnal umum dibuat otomatis saat transaksi pembelian/penjualan dengan status
                            <strong>completed/approved</strong>
                        </li>
                        <li><strong>Transaksi Pembelian:</strong>
                            <ul class="mb-0">
                                <li>Debit: Persediaan Barang Dagang (102)</li>
                                <li>Kredit: Kas (101)</li>
                            </ul>
                        </li>
                        <li><strong>Transaksi Penjualan:</strong>
                            <ul class="mb-0">
                                <li>Debit: Kas (101)</li>
                                <li>Kredit: Penjualan (401)</li>
                                <li>Debit: Harga Pokok Penjualan (601)</li>
                                <li>Kredit: Persediaan Barang Dagang (102)</li>
                            </ul>
                        </li>
                        <li>Total Debit harus sama dengan Total Kredit (Balance)</li>
                        <li>
                            @if (request('periode'))
                                @php
                                    $periode = request('periode');
                                    $date = \Carbon\Carbon::createFromFormat('Y-m', $periode);
                                    $namaBulan = $date->locale('id')->translatedFormat('F Y');
                                @endphp
                                <strong>Periode: {{ $namaBulan }}</strong>
                            @else
                                Pilih periode untuk menampilkan data jurnal umum
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

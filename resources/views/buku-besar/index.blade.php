@extends('layout.main')

@section('css')
    <style>
        .buku-besar-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: hidden;
        }

        .buku-besar-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
        }

        .buku-besar-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .buku-besar-header small {
            opacity: 0.9;
        }

        .buku-besar-body {
            padding: 0;
        }

        .table-buku-besar {
            margin: 0;
            font-size: 0.9rem;
        }

        .table-buku-besar th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px;
        }

        .table-buku-besar td {
            padding: 10px 12px;
        }

        .saldo-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .no-transaksi {
            background-color: #fff3cd;
            color: #856404;
            text-align: center;
            padding: 30px;
            font-style: italic;
        }

        .filter-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .badge-saldo-positif {
            background-color: #28a745;
        }

        .badge-saldo-negatif {
            background-color: #dc3545;
        }

        .badge-saldo-nol {
            background-color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Buku Besar</h5>
                <div class="card-header-action">
                    <span class="badge bg-info">Semua Akun COA</span>
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
                    <form method="GET" action="{{ route('buku-besar.index') }}">
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
                                    <a href="{{ route('buku-besar.index') }}" class="btn btn-secondary flex-fill">
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
                        $periodeReq = request('periode');
                        $date = \Carbon\Carbon::createFromFormat('Y-m', $periodeReq);
                        $namaBulan = $date->locale('id')->translatedFormat('F Y');
                    @endphp
                    <div class="alert alert-success mb-3">
                        <strong><i class="feather-calendar"></i> Menampilkan data periode: {{ $namaBulan }}</strong>
                    </div>
                @endif

                <!-- Buku Besar per Akun -->
                @if (request('periode'))
                    @foreach ($bukuBesar as $item)
                        <div class="buku-besar-card">
                            <div class="buku-besar-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>{{ $item['coa']->kode_akun }} - {{ $item['coa']->nama_akun }}</h5>
                                        <small>Buku Besar Periode {{ $namaBulan }}</small>
                                    </div>
                                    <div>
                                        @if ($item['saldo_akhir'] > 0)
                                            <span class="badge badge-saldo-positif fs-6">
                                                Saldo Akhir: Rp {{ number_format($item['saldo_akhir'], 0, ',', '.') }}
                                            </span>
                                        @elseif($item['saldo_akhir'] < 0)
                                            <span class="badge badge-saldo-negatif fs-6">
                                                Saldo Akhir: Rp {{ number_format($item['saldo_akhir'], 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge badge-saldo-nol fs-6">
                                                Saldo Akhir: Rp 0
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="buku-besar-body">
                                @if ($item['transaksis']->count() > 0)
                                    <table class="table table-bordered table-hover table-buku-besar mb-0">
                                        <thead>
                                            <tr>
                                                <th width="10%">Tanggal</th>
                                                <th width="15%">No. Referensi</th>
                                                <th>Keterangan</th>
                                                <th width="15%" class="text-end">Debit (Rp)</th>
                                                <th width="15%" class="text-end">Kredit (Rp)</th>
                                                <th width="15%" class="text-end">Saldo (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $saldo = $item['saldo_awal'];
                                            @endphp

                                            <tr class="saldo-row">
                                                <td colspan="3"><strong>Saldo Awal</strong></td>
                                                <td class="text-end">-</td>
                                                <td class="text-end">-</td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($saldo, 0, ',', '.') }}</strong>
                                                </td>
                                            </tr>

                                            @foreach ($item['transaksis'] as $transaksi)
                                                @php
                                                    $saldo += $transaksi->debit - $transaksi->kredit;
                                                @endphp
                                                <tr>
                                                    <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-secondary">{{ $transaksi->no_referensi }}</span>
                                                    </td>
                                                    <td>{{ $transaksi->keterangan }}</td>
                                                    <td class="text-end">
                                                        @if ($transaksi->debit > 0)
                                                            {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($transaksi->kredit > 0)
                                                            {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ number_format($saldo, 0, ',', '.') }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr class="saldo-row">
                                                <td colspan="3"><strong>TOTAL</strong></td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($item['total_debit'], 0, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($item['total_kredit'], 0, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ number_format($item['saldo_akhir'], 0, ',', '.') }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="no-transaksi">
                                        <i class="feather-info"></i> Tidak ada transaksi pada periode ini untuk akun
                                        ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Summary -->
                    <div class="alert alert-info mt-3">
                        <strong><i class="feather-info"></i> Ringkasan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Total Akun COA: <strong>{{ count($bukuBesar) }}</strong></li>
                            <li>Akun dengan Transaksi:
                                <strong>{{ collect($bukuBesar)->filter(fn($item) => $item['transaksis']->count() > 0)->count() }}</strong>
                            </li>
                            <li>Periode: <strong>{{ $namaBulan }}</strong></li>
                        </ul>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="feather-alert-circle"></i> Silakan pilih periode untuk menampilkan buku besar
                    </div>
                @endif

                <!-- Info -->
                <div class="alert alert-secondary mt-3">
                    <strong><i class="feather-info"></i> Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Buku Besar menampilkan detail transaksi per akun COA</li>
                        <li>Setiap akun menampilkan saldo awal, transaksi, dan saldo akhir</li>
                        <li>Saldo dihitung secara running balance (Saldo = Saldo Sebelumnya + Debit - Kredit)</li>
                        <li>Data diambil dari Jurnal Umum yang telah dibuat secara otomatis</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

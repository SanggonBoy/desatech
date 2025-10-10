@extends('layout.main')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .item-row {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
    </style>
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Tambah Transaksi Pembelian</h5>
                <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pembelian.store') }}" method="POST" id="formPembelian">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Pembelian <span class="text-danger">*</span></label>
                                <input type="text" name="no_pembelian" class="form-control" value="{{ $noPembelian }}"
                                    readonly required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembelian" class="form-control"
                                    value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gudang <span class="text-danger">*</span></label>
                                <select name="gudang_id" class="form-select select2" required>
                                    <option value="">Pilih Gudang</option>
                                    @foreach ($gudangs as $gudang)
                                        <option value="{{ $gudang->id }}"
                                            {{ old('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                            {{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih Gudang Mana Yang Akan Menjadi Tempat Penyimpanan
                                    Stok</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select select2" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved
                                    </option>
                                </select>
                                <small class="text-muted">Status "Approved" akan otomatis menyimpan stok ke gudang</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Detail Barang <span class="text-danger">*</span></label>
                        <div id="itemsContainer">
                            <!-- Item rows akan ditambahkan di sini -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addItemBtn">
                            <i class="feather-plus"></i> Tambah Barang
                        </button>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="mb-3">Total Pembelian</h5>
                                    <h2 id="totalPembelian" class="text-primary">Rp 0</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        window.barangData = @json($barangs);
    </script>
    <script src="{{ asset('custom/js/pembelian-create.js') }}"></script>
@endsection

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
                <h5 class="card-title">Edit Transaksi Pembelian</h5>
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

                <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST" id="formPembelian">
                    @csrf
                    @method('PUT')
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Pembelian <span class="text-danger">*</span></label>
                                <input type="text" name="no_pembelian" class="form-control"
                                    value="{{ old('no_pembelian', $pembelian->no_pembelian) }}" readonly required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembelian" class="form-control"
                                    value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian->format('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gudang <span class="text-danger">*</span></label>
                                <select name="gudang_id" class="form-select select2" required>
                                    <option value="">Pilih Gudang</option>
                                    @foreach ($gudangs as $gudang)
                                        <option value="{{ $gudang->id }}"
                                            {{ old('gudang_id', $pembelian->gudang_id) == $gudang->id ? 'selected' : '' }}>
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
                                            {{ old('supplier_id', $pembelian->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="draft"
                                        {{ old('status', $pembelian->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="approved"
                                        {{ old('status', $pembelian->status) == 'approved' ? 'selected' : '' }}>Approved
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
                            @foreach ($pembelian->details as $detail)
                                <div class="item-row" data-index="{{ $loop->index }}">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Barang</label>
                                            <select name="barang_id[]" class="form-select barang-select" required>
                                                <option value="">Pilih Barang</option>
                                                @foreach ($barangs as $barang)
                                                    <option value="{{ $barang->id }}"
                                                        data-satuan="{{ $barang->satuan_barang }}"
                                                        data-harga="{{ $barang->harga_beli ?? 0 }}"
                                                        {{ $detail->barang_id == $barang->id ? 'selected' : '' }}>
                                                        {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="jumlah[]" class="form-control jumlah-input"
                                                min="1" step="1" value="{{ $detail->jumlah }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Harga Satuan</label>
                                            <input type="number" name="harga_satuan[]" class="form-control harga-input"
                                                min="0" step="0.01" value="{{ $detail->harga_satuan }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Subtotal</label>
                                            <input type="text" class="form-control subtotal-display" readonly
                                                value="Rp {{ number_format($detail->subtotal, 0, ',', '.') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm w-100 remove-item">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $pembelian->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="mb-3">Total Pembelian</h5>
                                    <h2 id="totalPembelian" class="text-primary">Rp
                                        {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.barangData = @json($barangs);
        window.initialItemIndex = {{ $pembelian->details->count() }};
    </script>
    <script src="{{ asset('custom/js/pembelian-edit.js') }}"></script>
@endsection

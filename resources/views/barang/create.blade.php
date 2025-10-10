@extends('layout.main')

@section('content')
    <div class="col-xxl-10">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Tambah Barang</h5>
                <a href="/barang" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/barang" method="POST" id="barangForm">
                    @csrf
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Data Barang</h6>
                            <button type="button" class="btn btn-sm btn-success" id="addRow">
                                <i class="feather-plus"></i> Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="barangTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="40%">Nama Barang <span class="text-danger">*</span></th>
                                    <th width="30%">Satuan <span class="text-danger">*</span></th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="barangTableBody">
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <input type="text" name="barang[0][nama_barang]" class="form-control"
                                            placeholder="Masukkan nama barang" required>
                                    </td>
                                    <td>
                                        <select name="barang[0][satuan]" class="form-control" required>
                                            <option value="">Pilih Satuan</option>
                                            <option value="pcs">Pcs</option>
                                            <option value="box">Box</option>
                                            <option value="kg">Kg</option>
                                            <option value="liter">Liter</option>
                                            <option value="unit">Unit</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger removeRow" disabled>
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Semua</button>
                        <a href="/barang" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('custom/js/barang-create.js') }}"></script>
@endsection

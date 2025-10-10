@extends('layout.main')

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Tambah Supplier</h5>
                <a href="/supplier" class="btn btn-secondary">Kembali</a>
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

                <form action="/supplier" method="POST" id="supplierForm">
                    @csrf
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Data Supplier</h6>
                            <button type="button" class="btn btn-sm btn-success" id="addRow">
                                <i class="feather-plus"></i> Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="supplierTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama Supplier <span class="text-danger">*</span></th>
                                    <th width="20%">No. Telpon <span class="text-danger">*</span></th>
                                    <th width="35%">Alamat <span class="text-danger">*</span></th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="supplierTableBody">
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <input type="text" name="supplier[0][nama_supplier]" class="form-control"
                                            placeholder="Masukkan nama supplier" required>
                                    </td>
                                    <td>
                                        <input type="text" name="supplier[0][no_telp]" class="form-control"
                                            placeholder="Masukkan no. telpon" required>
                                    </td>
                                    <td>
                                        <textarea name="supplier[0][alamat]" class="form-control" rows="2" placeholder="Masukkan alamat" required></textarea>
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
                        <a href="/supplier" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('custom/js/supplier-create.js') }}"></script>
@endsection

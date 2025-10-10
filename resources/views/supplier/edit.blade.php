@extends('layout.main')

@section('css')
    <style>
        .whatsapp-btn-edit {
            background-color: #25D366 !important;
            border-color: #25D366 !important;
            transition: all 0.3s ease;
        }

        .whatsapp-btn-edit:hover {
            background-color: #128C7E !important;
            border-color: #128C7E !important;
            transform: scale(1.05);
        }

        .whatsapp-btn-edit:focus {
            box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25) !important;
        }

        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>
@endsection

@section('content')
    <div class="col-xxl-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Edit Supplier</h5>
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

                <form action="/supplier/{{ $supplier->id }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Kode Supplier</label>
                        <input type="text" class="form-control" value="{{ $supplier->kode_supplier }}" readonly>
                        <small class="text-muted">Kode supplier tidak dapat diubah</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supplier"
                            class="form-control @error('nama_supplier') is-invalid @enderror"
                            placeholder="Masukkan nama supplier"
                            value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
                        @error('nama_supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telpon <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                                placeholder="Masukkan no. telpon" value="{{ old('no_telp', $supplier->no_telp) }}" required>
                            <a href="https://wa.me/{{ $supplier->whatsapp_number }}" target="_blank"
                                class="btn btn-success whatsapp-btn-edit" title="Test WhatsApp" data-bs-toggle="tooltip"
                                data-original-number="{{ $supplier->whatsapp_number }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.384"
                                        fill="#ffffff" />
                                </svg>
                            </a>
                        </div>
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Klik ikon WhatsApp untuk test nomor</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="4"
                            placeholder="Masukkan alamat" required>{{ old('alamat', $supplier->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Supplier</button>
                        <a href="/supplier" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('custom/js/supplier-edit.js') }}"></script>
@endsection

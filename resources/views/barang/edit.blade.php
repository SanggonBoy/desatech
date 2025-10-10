@extends('layout.main')

@section('content')
    <div class="col-xxl-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Edit Barang</h5>
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

                <form action="/barang/{{ $barang->id }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" value="{{ $barang->kode_barang }}" readonly>
                        <small class="text-muted">Kode barang tidak dapat diubah</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang"
                            class="form-control @error('nama_barang') is-invalid @enderror"
                            placeholder="Masukkan nama barang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                            required>
                        @error('nama_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <select name="satuan" class="form-control @error('satuan') is-invalid @enderror" required>
                            <option value="">Pilih Satuan</option>
                            <option value="pcs" {{ old('satuan', $barang->satuan_barang) == 'pcs' ? 'selected' : '' }}>
                                Pcs</option>
                            <option value="box" {{ old('satuan', $barang->satuan_barang) == 'box' ? 'selected' : '' }}>
                                Box</option>
                            <option value="kg" {{ old('satuan', $barang->satuan_barang) == 'kg' ? 'selected' : '' }}>Kg
                            </option>
                            <option value="liter" {{ old('satuan', $barang->satuan_barang) == 'liter' ? 'selected' : '' }}>
                                Liter</option>
                            <option value="unit" {{ old('satuan', $barang->satuan_barang) == 'unit' ? 'selected' : '' }}>
                                Unit</option>
                        </select>
                        @error('satuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Barang</button>
                        <a href="/barang" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

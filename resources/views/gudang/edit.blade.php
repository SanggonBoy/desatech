@extends('layout.main')

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Edit Gudang</h5>
                <a href="{{ route('gudang.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('gudang.update', $gudang->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Gudang <span class="text-danger">*</span></label>
                                <input type="text" name="kode_gudang" class="form-control"
                                    value="{{ old('kode_gudang', $gudang->kode_gudang) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Gudang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_gudang" class="form-control"
                                    value="{{ old('nama_gudang', $gudang->nama_gudang) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PIC (Person In Charge)</label>
                                <input type="text" name="pic" class="form-control"
                                    value="{{ old('pic', $gudang->pic) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control"
                                    value="{{ old('no_telp', $gudang->no_telp) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $gudang->alamat) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="aktif"
                                        {{ old('status', $gudang->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif"
                                        {{ old('status', $gudang->status) == 'nonaktif' ? 'selected' : '' }}>Non-Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

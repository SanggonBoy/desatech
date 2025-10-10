@extends('layout.main')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Dashboard</h3>
                <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->name }}!</p>
            </div>
        </div>

        <div class="row mb-4">
            {{-- <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Total Inventory Value</p>
                                <h3 class="mb-0">Rp {{ number_format(900000, 0, ',', '.') }}</h3>
                                <small class="text-muted">12000 Produk</small>
                            </div>
                            <div class="avatar-text avatar-lg bg-primary text-white">
                                <i data-feather="package" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection

@section('js')
@endsection

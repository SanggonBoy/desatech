@extends('layout.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="col-xxl-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Master Data Chart of Accounts (COA)</h5>
                <div class="card-header-action">
                    <span class="badge bg-info">Read Only - Data Default Sistem</span>
                </div>
            </div>
            <div class="card-body custom-card-action">
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

                <div class="table-responsive">
                    <table class="table table-hover" id="coaTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Kode Akun</th>
                                <th>Nama Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coas as $index => $coa)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $coa->kode_akun }}</td>
                                    <td>{{ $coa->nama_akun }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('custom/js/coa.js') }}"></script>
@endsection

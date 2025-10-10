<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/" class="b-brand">
                PT. IBU RAMA
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item {{ request()->is('/') ? 'active' : '' }}">
                    <a href="/" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Master Data</label>
                </li>
                <li class="nxl-item nxl-hasmenu {{ request()->is('barang*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-package"></i></span>
                        <span class="nxl-mtext">Barang</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->is('barang') ? 'active' : '' }}">
                            <a class="nxl-link" href="/barang">Lihat Barang</a>
                        </li>
                        <li class="nxl-item {{ request()->is('barang/create') ? 'active' : '' }}">
                            <a class="nxl-link" href="/barang/create">Tambah Barang</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->is('supplier*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-truck"></i></span>
                        <span class="nxl-mtext">Supplier</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->is('supplier') ? 'active' : '' }}">
                            <a class="nxl-link" href="/supplier">Lihat Supplier</a>
                        </li>
                        <li class="nxl-item {{ request()->is('supplier/create') ? 'active' : '' }}">
                            <a class="nxl-link" href="/supplier/create">Tambah Supplier</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->is('gudang*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Gudang</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->is('gudang') ? 'active' : '' }}">
                            <a class="nxl-link" href="/gudang">Lihat Gudang</a>
                        </li>
                        <li class="nxl-item {{ request()->is('gudang/create') ? 'active' : '' }}">
                            <a class="nxl-link" href="/gudang/create">Tambah Gudang</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->is('pelanggan*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Pelanggan</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->is('pelanggan*') ? 'active' : '' }}">
                            <a class="nxl-link" href="/pelanggan">Lihat Pelanggan</a>
                        </li>
                        <li class="nxl-item {{ request()->is('/pelanggan/create') ? 'active' : '' }}">
                            <a class="nxl-link" href="/pelanggan/create">Tambah Pelanggan</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item {{ request()->is('coa') ? 'active' : '' }}">
                    <a href="/coa" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-list"></i></span>
                        <span class="nxl-mtext">Chart of Accounts</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Transaksi</label>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->is('pembelian') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shopping-cart"></i></span>
                        <span class="nxl-mtext">Pembelian</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->is('pembelian*') }}">
                            <a class="nxl-link" href="/pembelian">Lihat Pembelian</a>
                        </li>
                        <li class="nxl-item {{ request()->is('/pembelian/create') ? 'active' : '' }}">
                            <a class="nxl-link" href="/pembelian/create">Tambah Pembelian</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->is('penjualan') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-truck"></i></span>
                        <span class="nxl-mtext">Penjualan</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->is('penjualan*') }}">
                            <a class="nxl-link" href="/penjualan">Lihat Penjualan</a>
                        </li>
                        <li class="nxl-item {{ request()->is('/penjualan/create') ? 'active' : '' }}">
                            <a class="nxl-link" href="/penjualan/create">Tambah Penjualan</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Laporan</label>
                </li>

                <li class="nxl-item {{ request()->is('jurnal-umum') ? 'active' : '' }}">
                    <a href="/jurnal-umum" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-book-open"></i></span>
                        <span class="nxl-mtext">Jurnal Umum</span>
                    </a>
                </li>

                <li class="nxl-item {{ request()->is('buku-besar') ? 'active' : '' }}">
                    <a href="/buku-besar" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-book"></i></span>
                        <span class="nxl-mtext">Buku Besar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

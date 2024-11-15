<aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('logo.png') }}" alt="AdminLTE Logo"
            class="brand-image " style="opacity: .8">
        <span class="brand-text font-weight-light">Kompensasi JTI</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- SidebarSearch Form -->
        <div class="form-inline mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/riwayat') }}" class="nav-link {{ $activeMenu == 'riwayat' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Riwayat Kompensasi</p>
                    </a>
                </li>
                <li class="nav-header">Validasi Registrasi</li>
                <li class="nav-item">
                    <a href="{{ url('/validasi') }}" class="nav-link {{ $activeMenu == 'validasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-check-double"></i>
                        <p>Validasi Registrasi</p>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        </span>
                    </a>
                </li>
                <li class="nav-header">Manage Pengguna</li>
                <li class="nav-item">
                    <a href="{{ url('/level') }}" class="nav-link {{ $activeMenu == 'level' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Role</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/user') }}" class="nav-link {{ $activeMenu == 'user' ? 'active' : '' }}">
                        <i class="nav-icon far fa-user"></i>
                        <p>Data User</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/mahasiswa') }}" class="nav-link {{ $activeMenu == 'mhs' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Data Mahasiswa</p>
                    </a>
                </li>

                </li>
                <li class="nav-header">Dosen/Tendik</li>
                <li class="nav-item">
                    <a href="{{ url('/dosen') }}" class="nav-link {{ $activeMenu == 'dosen' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>Tambah Pekerjaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/penerimaan') }}" class="nav-link {{ $activeMenu == 'penerimaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Penerimaan</p>
                    </a>
                </li>
                <li class="nav-header">Kaprodi</li>
                <li class="nav-item">
                    <a href="{{ url('/penerimaan') }}" class="nav-link {{ $activeMenu == 'penerimaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Penerimaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/riwayat') }}" class="nav-link {{ $activeMenu == 'riwayat' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Riwayat Penerimaan</p>
                    </a>
                </li>
                <li class="nav-header">Mahasiswa</li>
                <li class="nav-item">
                    <a href="{{ url('/pekerjaan') }}" class="nav-link {{ $activeMenu == 'pekerjaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Pekerjaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/riwayat') }}" class="nav-link {{ $activeMenu == 'riwayat' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Riwayat Pekerjaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/notifikasi') }}" class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/kompetensi') }}" class="nav-link {{ $activeMenu == 'kompetensi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-id-card"></i>
                        <p>Kompetensi</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>

@push('js')
    <script>
        $(document).ready(function() {
    $.ajax({
        url: '{{ url("/hitung-notif") }}',
        method: 'GET',
        success: function(response) {
            var jumlahNotif = response.jumlah;
            if (jumlahNotif > 99) {
                $('.badge').text('99+');
            } else {
                $('.badge').text(jumlahNotif);
            }
        }
    });
});
    </script>
@endpush

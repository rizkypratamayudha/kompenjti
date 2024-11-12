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
                <li class="nav-header">Validasi Registrasi</li>
                <li class="nav-item">
                    <a href="{{ url('/validasi') }}" class="nav-link {{ $activeMenu == 'validasi' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-check-double"></i>
                        <p>Validasi Registrasi</p>
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
                <li class="nav-header">Manage Riwayat Kompen</li>
                <li class="nav-item">

                </li>
                <li class="nav-header">Dosen/Tendik</li>
                <li class="nav-item">
                    <a href="{{ url('/dosen') }}" class="nav-link {{ $activeMenu == 'dosen' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>Tambah Pekerjaan</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>

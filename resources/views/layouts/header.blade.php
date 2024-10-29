<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="../../index3.html" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="modal" data-target="#profileModal" role="button">
                {{-- @if(Auth::user()->avatar)
                    <img style="width: 25px; height: 25px; object-fit: cover;"  class="img-fluid rounded-circle" src="{{ asset('images/' . Auth::user()->avatar) }}" alt="User Avatar">
                @else --}}
                    <i class="fas fa-user"></i>
                {{-- @endif --}}
            </a>
        </li>
    </ul>
</nav>

<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title w-100" id="profileModalLabel">Profile anda</h5>
            </div>
            <div class="modal-body text-center">
                <div class="profile-info">
                    <div>
                        <img src="{{ Auth::user()->avatar ? asset('images/' . Auth::user()->avatar) : asset('user.png') }}"
                alt="User Avatar"
                class="img-fluid rounded-circle"
                style="width: 120px; height: 120px; object-fit: cover;">
                    </div> <!--avatar profile yang bisa berupa gambar-->
                    <div class="mb-2 mt-2">
                        <h4 class="font-weight-bold">{{Auth::user()->username}}</h4>
                    </div> <!--username yang login -->
                    <div>
                        <span style="font-size: 16px; padding: 8px 15px;">{{Auth::user()->level->level_nama}}</span>
                    </div> <!--level_nama dari username yang login-->
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="{{url('profile/edit/')}}" class="btn btn-info">Edit Profile</a>
                <a id="logout-link" class="btn btn-danger" href="{{url('logout/')}}">Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('logout-link').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        // Trigger SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Apakah yakin ingin keluar?',
            text: "Session anda akan berakhir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Log Out',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // If user confirms, redirect to the logout URL
                window.location.href = "{{ url('logout/') }}";
            }
        });
    });
</script>

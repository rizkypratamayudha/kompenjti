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
            <a href="{{url('/contact')}}" class="nav-link">Contact</a>
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
         <!-- Profile Icon -->
<li class="nav-item">
    <a id="profile" class="nav-link" href="#" role="button">
        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('user.png') }}"
            alt="User Avatar" class="img-circle" width="25" height="25" style="object-fit: cover;">
    </a>
</li>

        <!-- Logout Form -->
        <form id="logout-form" action="{{ url('logout') }}" method="post" style="display: none;">
            @csrf
        </form>
    </ul>
</nav>
<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> <!-- Modal dialog centered -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Avatar -->
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('user.png') }}"
                    alt="User Avatar" class="img-circle mb-3" width="100" height="100"
                    tyle="object-fit: cover;">


                <!-- Profile Information with Icons -->
                <p><strong><i class="fas fa-user"></i> Username:</strong> {{ auth()->user()->username }}</p>
                <p><strong><i class="fas fa-id-card"></i> Nama:</strong> {{ auth()->user()->nama }}</p>
                <p><strong><i class="fas fa-user-tag"></i> Level:</strong>
                    {{ auth()->user()->level ? auth()->user()->level->level_nama : 'Tidak ada level' }}</p>
            </div>

            <div class="modal-footer justify-content-center"> <!-- Centered footer -->
                <!-- Logout Button with Icon -->
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>

                <!-- Edit Profile Button with Icon -->
                <a href="{{ url('profile/edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>

        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Profile Modal Script -->
<script>
    document.getElementById('profile').addEventListener('click', function(e) {
        e.preventDefault();
        $('#profileModal').modal('show'); // Trigger the modal
    });

    document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Apakah Anda yakin ingin keluar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, keluar!',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
});

</script>

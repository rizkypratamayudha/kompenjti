@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Bagian Kiri: Profil Foto -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <!-- Placeholder Gambar Profil -->
                        <div class="profile-picture mb-3">
                            <!-- Avatar di halaman profil -->
                            <img id="profile-avatar"
                                src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('user.png') }}"
                                alt="User Avatar" class="img-circle rounded-circle" width="100" height="100"
                                style="object-fit: cover;">
                        </div>

                        <!-- Nama Pengguna -->
                        <h4>{{ auth()->user()->nama ?? 'user' }}</h4>
                        <!-- Form untuk Mengganti Foto Profil -->
                        <form action="{{ url('profile/update_profile') }}" method="POST" enctype="multipart/form-data"
                            class="mt-3">
                            @csrf
                            <div class="form-group text-center">
                                <!-- Wrapper untuk custom file input -->
                                <div class="custom-file">
                                    <!-- Input file yang asli (disembunyikan) -->
                                    <input type="file" class="custom-file-input" id="avatar" name="avatar"
                                        accept="image/*" required>

                                    <!-- Label yang menampilkan tombol "Browse" -->
                                    <label class="custom-file-label" for="avatar">Browse</label>
                                </div>

                                <!-- Tombol Ganti Profil dengan Ikon -->
                                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-user-edit"></i> Ganti
                                    Profil</button>
                            </div>
                        </form>

                        <!-- Form untuk Menghapus Foto Profil -->
                        <form action="{{ url('profile/delete_avatar') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> Hapus Profil
                            </button>
                        </form>
                        <script>
                            document.getElementById('avatar').addEventListener('change', function() {
                                var fileInput = document.getElementById('avatar');
                                var fileName = fileInput.files.length > 0 ? fileInput.files[0].name :
                                    "Browse"; // Nama file yang dipilih
                                var label = document.querySelector('.custom-file-label');

                                // Menampilkan nama file di label
                                label.textContent = fileName;
                            });
                        </script>
                        <style>
                            .custom-file-input {
                                position: absolute;
                                width: 100%;
                                height: 100%;
                                top: 0;
                                left: 0;
                                opacity: 0;
                                cursor: pointer;
                            }

                            .custom-file-label {
                                position: relative;
                                padding: 0.5rem;
                                height: 100%;
                                width: 100%;
                                border-left: 1px solid #c1c4c4e6;
                                background-color: white;
                                /* Gray background for 'Browse' button */
                                color: #000;
                                text-align: left;
                                line-height: 1.5rem;
                                padding-right: 1rem;
                                cursor: pointer;
                            }
                        </style>



                    </div>
                </div>
            </div>
            <!-- Bagian Kanan: Edit Profil -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <!-- Tab-style Navigation -->
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#editProfile" data-toggle="tab">Edit Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#editPassword" data-toggle="tab">Edit Password</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body tab-content">
                        <!-- Flash Messages for Success/Error -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Tab Content for Edit Profile -->
                        <div class="tab-pane active" id="editProfile">
                            <!-- Edit Profile Form -->
                            <form id="formUpdate" action="{{ url('profile/update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <!-- Username -->
                                <div class="form-group row">
                                    <label for="username" class="col-md-4 col-form-label">{{ __('Username') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="username"
                                            value="{{ auth()->user()->username }}">
                                    </div>
                                </div>
                                <!-- Nama -->
                                <div class="form-group row">
                                    <label for="nama" class="col-md-4 col-form-label">{{ __('Nama') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="nama"
                                            value="{{ old('nama', auth()->user()->nama) }}">
                                    </div>
                                </div>
                                <!-- Level (Readonly) -->
                                <div class="form-group row">
                                    <label for="level_nama" class="col-md-4 col-form-label">{{ __('Level') }}</label>
                                    <div class="col-md-8">
                                        <!-- Input untuk menampilkan level_nama (readonly) -->
                                        <input type="text" class="form-control" id="level_nama"
                                            value="{{ $level_nama }}" readonly>
                                        <!-- Input tersembunyi untuk menyimpan level_id -->
                                        <input type="hidden" name="level_id" value="{{ $level_id }}">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>

                                    </div>
                                </div>
                            </form>

                        </div>

                        <!-- Tab Content for Edit Password -->
                        <div class="tab-pane" id="editPassword">
                            <form id="formPasswordUpdate" action="{{ url('profile/update_password') }}" method="POST">
                                @csrf
                                @method('PUT') <!-- Menggunakan method PUT sesuai standar untuk update data -->

                                <!-- Current Password -->
                                <div class="form-group row">
                                    <label for="current_password"
                                        class="col-md-4 col-form-label">{{ __('Password Lama') }}</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="current_password" required>
                                        <small id="error-current_password" class="error-text form-text text-danger">
                                            @error('current_password')
                                                {{ $message }}
                                            @enderror
                                        </small>
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div class="form-group row">
                                    <label for="password"
                                        class="col-md-4 col-form-label">{{ __('Password Baru') }}</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="password" required>
                                        <small id="error-password" class="error-text form-text text-danger">
                                            @error('password')
                                                {{ $message }}
                                            @enderror
                                        </small>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group row">
                                    <label for="password_confirmation"
                                        class="col-md-4 col-form-label">{{ __('Konfirmasi Password') }}</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="password_confirmation"
                                            required>
                                        <small id="error-password_confirmation" class="error-text form-text text-danger">
                                            @error('password_confirmation')
                                                {{ $message }}
                                            @enderror
                                        </small>
                                    </div>
                                </div>

                                <!-- Submit Button for Password Update -->
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-lock"></i> Update Password
                                        </button>

                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        // Validasi hapus foto profile
        document.querySelector('form[action="{{ url('profile/delete_avatar') }}"]').addEventListener('submit', function(
        e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Apakah Anda yakin ingin menghapus foto profil?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-warning',
                    cancelButton: 'btn btn-primary'
                },
                buttonsStyling: false,
                didRender: function() {
                    const confirmButton = Swal.getConfirmButton();
                    confirmButton.style.marginRight = '10px';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status) {
                                Swal.fire('Deleted!', data.message, 'success');

                                // Update avatar in header
                                const avatarImage = document.querySelector('img[alt="User Avatar"]');
                                avatarImage.src = '{{ asset('user.png') }}';

                                // Update avatar in index view (profile page)
                                const profileAvatar = document.getElementById('profile-avatar');
                                if (profileAvatar) {
                                    profileAvatar.src =
                                    '{{ asset('user.png') }}'; // Memperbarui src avatar di halaman profil
                                }
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan pada server: ' + error.message,
                                'error');
                        });
                }
            });
        });


        // Fungsi untuk memperbarui avatar di tampilan indeks
        function updateIndexAvatar(defaultAvatar) {
            const indexAvatar = document.querySelector(
            'img[index-avatar="user-avatar"]'); // Ganti selector sesuai dengan elemen di halaman indeks
            if (indexAvatar) {
                indexAvatar.src = defaultAvatar; // Memperbarui src dengan gambar default
            }
        }


        // Validasi dan Ajax untuk form ganti password
        $(document).ready(function() {
            $("#formPasswordUpdate").validate({
                rules: {
                    password: {
                        required: true,
                        minlength: 5
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "[name='password']" // Field harus cocok dengan password
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'PUT', // Change this to 'PUT'
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Password Berhasil Diupdate',
                                    text: response.message
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                    });

                    return false; // Mencegah default submit form
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        $(document).ready(function() {
            $("#formUpdate").validate({
                rules: {
                    level_id: {
                        required: true,
                    },
                    username: {
                        required: false,
                        minlength: 3,
                    },
                    nama: {
                        required: false,
                        minlength: 3,
                        maxlength: 100,
                    },
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: $(form).attr('action'),
                        type: $(form).attr('method'),
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                            } else {
                                // Menampilkan pesan error
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                    });
                    return false; // Mencegah default submit form
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endpush

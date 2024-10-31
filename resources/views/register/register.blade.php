<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="../../index2.html" class="h1">Register</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Registrasi akun baru</p>
                <form action="{{ url('register') }}" method="POST" id="form-register">
                    @csrf
                    <div class="input-group mb-3">
                        <select class="form-control" id="level_id" name="level_id" required>
                            <option value="">- Pilih Role -</option>
                            @foreach ($level as $item)
                                <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-level-down-alt"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="nama" name="nama" type="text" class="form-control" placeholder="Full Name" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user-tag"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="username" name="username" type="number" class="form-control" placeholder="NIM/NIP" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append" >
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div id="additional-fields">
                        <div class="input-group mb-3" id="email-field" style="display: none;" >
                            <input id="email" name="email" type="email" class="form-control"
                                placeholder="Email" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3" id="no-hp-field" style="display: none;" >
                            <input id="no_hp" name="no_hp" type="number" class="form-control"
                                placeholder="No HP" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-phone"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3" id="prodi-field" style="display: none;">
                            <select class="form-control" id="prodi_id" name="prodi_id" required>
                                <option value="">- Pilih Prodi -</option>
                                @foreach ($prodi as $item)
                                    <option value="{{ $item->prodi_id }}">{{ $item->prodi_nama }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-level-down-alt"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3" id="angkatan-field" style="display: none;">
                            <input id="angkatan" name="angkatan" type="number" min="2018" max="2024" class="form-control"
                                placeholder="Angkatan" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-calendar-alt"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <a href="{{ url('login') }}" class="text-center">Sudah punya akun?</a>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Bootstrap 4 -->
    <script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="adminlte/dist/js/adminlte.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show/hide fields based on role selection
            $('#level_id').change(function() {
                var selectedRole = $(this).val();

                $('#email-field, #no-hp-field, #prodi-field, #angkatan-field').hide();

                if (selectedRole == '3') { // Mahasiswa
                    $('#email-field, #no-hp-field, #prodi-field, #angkatan-field').show();
                    // Set rules for Mahasiswa
                    $("#email").rules("add", { required: true, email: true }); // Add email validation
                    $("#no_hp").rules("add", { required: true });
                    $("#prodi_id").rules("add", { required: true });
                    $("#angkatan").rules("add", { required: true });
                } else if (selectedRole == '2' || selectedRole == '4' || selectedRole == '1') { // Dosen or Kaprodi
                    $('#email-field, #no-hp-field').show();
                    // Set rules for Dosen or Kaprodi
                    $("#email").rules("add", { required: true, email: true }); // Add email validation
                    $("#no_hp").rules("add", { required: true });
                    $("#prodi_id").rules("remove", "required"); // Remove required for prodi_id
                    $("#angkatan").rules("remove", "required"); // Remove required for angkatan
                }
            });

            // Validate the form
            $("#form-register").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        maxlength: 20
                    },
                    level_id: {
                        required: true,
                    },
                    nama: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) { // if success
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Mohon Tunggu Verifikasi\nDari Admin',
                                    text: response.message,
                                }).then(function() {
                                    window.location = response.redirect;
                                });
                            } else { // if error
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>

</html>

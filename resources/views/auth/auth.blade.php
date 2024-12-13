<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KompenJTI</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        :root {
            --primary-blue: #024CAA;
            /* Prioritized blue */
            --light-blue: #008DDA;
            /* Light blue */
            --medium-blue: #82C4F3;
            /* Medium blue */
            --dark-blue: #0C356A;
            /* Dark blue */
        }

        /* Menggunakan warna biru yang telah didefinisikan di :root */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--light-blue);
            /* Background light blue */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            width: 100vw;
        }

        h1 {
            font-weight: bold;
            margin: 0;
            color: var(--dark-blue);
            /* Dark blue for headers */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--medium-blue);
            /* Medium blue for sub-headers */
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
            color: var(--medium-blue);
            /* Medium blue for text */
        }

        span {
            font-size: 12px;
            color: var(--primary-blue);
            /* Primary blue for smaller text */
        }

        a {
            color: var(--medium-blue);
            /* Medium blue for links */
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid var(--medium-blue);
            /* Medium blue border */
            background-color: var(--dark-blue);
            /* Medium blue for button background */
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: var(--medium-blue);
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: var(--medium-blue);
            /* Light blue for inputs */
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .input-group {
            width: 100%;
        }

        #additional-fields .input-group {
            width: 100%;
        }

        .input-group.mb-3 {
            margin-bottom: 15px;
        }

        .form-control {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid var(--medium-blue);
            /* Medium blue border for form controls */
            font-size: 14px;
            width: 100%;
        }

        #additional-fields select.form-control {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid var(--medium-blue);
        }

        #additional-fields .input-group input,
        #additional-fields select {
            padding: 10px;
            margin-top: 5px;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),
                0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            width: 100%;
            max-width: 1000px;
            height: 600px;
            /* Fixed height */
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }

        .form-container {
            position: absolute;
            width: 50%;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .form-container form {
            height: 100%;
            overflow-y: auto;
            padding: 30px 50px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container form::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .form-container form::-webkit-scrollbar-thumb {
            background: var(--medium-blue);
            border-radius: 4px;
        }

        .form-container form::-webkit-scrollbar-thumb:hover {
            background: var(--dark-blue);
        }

        .sign-in-container {
            left: 0;
            z-index: 2;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .sign-up-container {
            left: 0;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {

            0%,
            49.99% {
                opacity: 0;
                z-index: 1;
            }

            50%,
            100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: var(--medium-blue);
            /* Medium blue background for overlay */
            background: -webkit-linear-gradient(to right, var(--medium-blue), var(--dark-blue));
            background: linear-gradient(to right, var(--medium-blue), var(--dark-blue));
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .social-container {
            margin: 20px 0;
        }

        .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }

        /* Add some spacing between form elements */
        .input-group {
            margin-bottom: 15px;
            width: 100%;
        }

        .form-container form input,
        .form-container form select,
        .form-container form button {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2 class="mt-5">KOMPEN JTI</h2><br><br>
    <div class="container" id="container">
        <div class="form-container sign-in-container">
            <form action="{{ url('login') }}" method="POST" id="form-login">
                @csrf
                <h1 class="mb-3">Sign in</h1>

                <div class="input-group mb-3">
                <input class="input-group mb-3" type="text" id="username" name="username" placeholder="Username atau Nim">
                <small id="error-username" class="error-text text-danger"></small>
                </div>
                <div class="input-group mb-3">
                <input type="password" id="password" name="password" placeholder="Password">
                <small id="error-password" class="error-text text-danger"></small>
                </div>
                <button type="submit" class="mt-4">Sign In</button>
            </form>
        </div>
        <div class="form-container sign-up-container">
            <form action="{{ url('register') }}" method="POST" id="form-register">
                @csrf
                <h1>Sign up</h1><br><br>
                <div class="input-group mb-3">
                    <select class="form-control" id="level_id" name="level_id" required>
                        <option value="">- Pilih Role -</option>
                        @if (isset($level) && $level->isNotEmpty())
                            @foreach ($level as $lvl)
                                <option value="{{ $lvl->level_id }}">{{ $lvl->level_nama }}</option>
                            @endforeach
                        @else
                            <option value="">Level tidak tersedia</option>
                        @endif
                    </select>
                </div>
                <div class="input-group mb-3">
                    <input id="nama" name="nama" type="text" class="form-control" placeholder="Full Name"
                        required>
                </div>
                <div class="input-group mb-3">
                    <input id="username" name="username" type="number" class="form-control" placeholder="NIM/NIP"
                        required>
                </div>
                <div class="input-group mb-3">
                    <input id="password" name="password" type="password" class="form-control" placeholder="Password"
                        required>
                    <div class="input-group-append">
                        <span class="input-group-text border-0 bg-transparent"
                            style="margin-left: -40px; z-index: 100;"></span>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                        placeholder="Ulang Password" required>
                    <div class="input-group-append">
                        <span class="input-group-text border-0 bg-transparent"
                            style="margin-left: -40px; z-index: 100;"></span>
                    </div>
                </div>
                
                    <div class="input-group mb-3" id="email-field" style="display: none;">
                        <input id="email" name="email" type="email" class="form-control" placeholder="Email"
                            required>
                    </div>

                    <div class="input-group mb-3" id="no-hp-field" style="display: none;">
                        <input id="no_hp" name="no_hp" type="number" class="form-control" placeholder="No HP"
                            required>
                    </div>

                    <div class="input-group mb-3" id="prodi-field" style="display: none;">
                        <select class="form-control" id="prodi_id" name="prodi_id" required>
                            <option value="">- Pilih Prodi -</option>
                            @if (isset($prodi) && $prodi->isNotEmpty())
                                @foreach ($prodi as $p)
                                    <option value="{{ $p->prodi_id }}">{{ $p->prodi_nama }}</option>
                                @endforeach
                            @else
                                <option value="">Prodi tidak tersedia</option>
                            @endif
                        </select>
                    </div>

                    <div class="input-group mb-3" id="angkatan-field" style="display: none;">
                        <input id="angkatan" name="angkatan" type="number" min="2018" max="2024"
                            class="form-control" placeholder="Angkatan" required>
                    </div>

                    <div class="input-group mb-3" id="periode-field" style="display: none;">
                        <select class="form-control" id="periode_id" name="periode_id" required>
                            <option value="">- Pilih Periode -</option>
                            @if (isset($periode) && $periode->isNotEmpty())
                                @foreach ($periode as $per)
                                    <option value="{{ $per->periode_id }}">{{ $per->periode_nama }}</option>
                                @endforeach
                            @else
                                <option value="">Periode tidak tersedia</option>
                            @endif
                        </select>
                    </div>


                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p style="color: white">To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>

                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>

                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        signUpButton.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $("#form-login").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    },
                    password: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            console.log(response)
                            if (response.status) { // jika sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                }).then(function() {
                                    window.location = response.redirect;
                                });
                            } else { // jika error
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
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
        $(document).ready(function() {
            // Show/hide fields based on role selection
            $('#level_id').change(function() {
                var selectedRole = $(this).val();

                $('#email-field, #no-hp-field, #prodi-field, #angkatan-field, #periode-field').hide();

                if (selectedRole == '3') {
                    $('#email-field, #no-hp-field, #prodi-field, #angkatan-field, #periode-field').show();
                    $("#email").rules("add", {
                        required: true,
                        email: true
                    });
                    $("#no_hp").rules("add", {
                        required: true
                    });
                    $("#prodi_id").rules("add", {
                        required: true
                    });
                    $("#angkatan").rules("add", {
                        required: true
                    });
                    $("#periode_id").rules("add", {
                        required: true
                    });
                } else if (selectedRole == '4') {
                    $('#email-field, #no-hp-field, #prodi-field').show();
                    $("#email").rules("add", {
                        required: true,
                        email: true
                    });
                    $("#no_hp").rules("add", {
                        required: true
                    });
                    $("#prodi_id").rules("add", {
                        required: true
                    });
                    $("#angkatan").rules("remove", "required");
                    $("#periode_id").rules("remove", "required");
                } else if (selectedRole == '1' || selectedRole == '2') {
                    $('#email-field, #no-hp-field').show();
                    $("#email").rules("add", {
                        required: true,
                        email: true
                    });
                    $("#no_hp").rules("add", {
                        required: true
                    });
                    $("#prodi_id").rules("remove", "required");
                    $("#angkatan").rules("remove", "required");
                    $("#periode_id").rules("remove", "required");
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

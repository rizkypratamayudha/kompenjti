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
    <style>
        body {
          background: linear-gradient(#050C9C, #3572EF, #3ABEF9, #A7E6FF) !important; /* Gradasi biru */
          font-family: 'Source Sans Pro', sans-serif;
          position: relative;
          overflow: hidden;
        }
    
        /* Adding background image with blur */
        body::before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: url('download.jpeg') no-repeat center center;
          background-size: cover;
          filter: blur(15px); /* Apply blur to the background image */
          z-index: 1;
          opacity: 0.5; /* Adjust opacity to blend with gradient */
        }
    
        .register-box {
          position: relative;
          z-index: 2; /* Ensures the form appears above the background */
          width: 400px;
          margin: 80px auto;
        }
    
        .card {
          border-radius: 15px;
          box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }
    
        .card-header {
          background: linear-gradient(135deg, #0077B6, #023E8A); /* Gradasi biru tua */
          color: white;
          font-weight: bold;
          font-size: 20px;
          border-radius: 15px 15px 0 0;
        }
    
        .btn-primary {
          background: linear-gradient(135deg, #0077B6, #023E8A); /* Gradasi biru untuk tombol */
          border-color: #00509D;
        }
    
        .btn-primary:hover {
          background: linear-gradient(135deg, #00509D, #004080); /* Gradasi biru lebih gelap untuk hover */
          border-color: #004080;
        }
    
        .form-control {
          border-radius: 10px;
        }
    
        .input-group-text {
          border-radius: 0 10px 10px 0;
          background-color: #EAEAEA;
        }
    
        .input-group .form-control.is-invalid {
          border-color: #e3342f;
        }
    
        .input-group .form-control.is-invalid ~ .input-group-text {
          border-color: #e3342f;
        }

        .toggle-password {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-left: none;
        }

        .toggle-password:focus {
            box-shadow: none;
        }

        .input-group-append .input-group-text {
            border-radius: 0;
        }

        .input-group-append button {
            z-index: 0;
        }
    
        .card-body {
          padding: 30px;
        }
    
        .mb-0 a {
          color: #0077B6;
          font-weight: bold;
        }
    
        .mb-0 a:hover {
          text-decoration: underline;
          color: #004080;
        }
    
        .custom-margin {
            margin-top: 20px; /* Adjust the value as needed */
        }
    
      </style>
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="../../index2.html" class="h1">Register</a>
            </div>
            <div class="card-body">
  
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
                        <div class="input-group-append">
                            <span class="input-group-text border-0 bg-transparent" style="margin-left: -40px; z-index: 100;">
                                <i class="fas fa-eye toggle-password" data-target="password" style="cursor: pointer;"></i>
                            </span>
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Ulang Password" required>
                        <div class="input-group-append">
                            <span class="input-group-text border-0 bg-transparent" style="margin-left: -40px; z-index: 100;">
                                <i class="fas fa-eye toggle-password" data-target="password_confirmation" style="cursor: pointer;"></i>
                            </span>
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
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                      </div>
                    </form>
                    <p class="mb-0 text-center custom-margin"> <!-- Added custom class -->
                      <a href="{{ url('login') }}">I already have a membership</a>
                  </p>
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
        $(document).on('click', '.toggle-password', function() {
            const icon = $(this);
            const targetId = icon.data('target');
            const input = $(`#${targetId}`);
            
            // Toggle password visibility
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        $(document).ready(function() {
            // Show/hide fields based on role selection
            $('#level_id').change(function() {
                var selectedRole = $(this).val();
                
                $('#email-field, #no-hp-field, #prodi-field, #angkatan-field').hide();
                
                if (selectedRole == '3' ) {
                    $('#email-field, #no-hp-field, #prodi-field, #angkatan-field').show();
                    $("#email").rules("add", { required: true, email: true });
                    $("#no_hp").rules("add", { required: true });
                    $("#prodi_id").rules("add", { required: true });
                    $("#angkatan").rules("add", { required: true });
                } 
                else if (selectedRole == '4') {
                    $('#email-field, #no-hp-field, #prodi-field').show(); 
                    $("#email").rules("add", { required: true, email: true });
                    $("#no_hp").rules("add", { required: true });
                    $("#prodi_id").rules("add", { required: true }); 
                    $("#angkatan").rules("remove", "required"); 
                }
                else if (selectedRole == '1' || selectedRole == '2') {
                    $('#email-field, #no-hp-field').show();
                    $("#email").rules("add", { required: true, email: true });
                    $("#no_hp").rules("add", { required: true });
                    $("#prodi_id").rules("remove", "required");
                    $("#angkatan").rules("remove", "required");
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

<form action="{{ url('/user/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Role Selection -->
                <div class="form-group">
                    <label>Role Pengguna</label>
                    <select name="level_id" id="level_id" class="form-control" required>
                        <option value="">- Pilih Role -</option>
                        @foreach ($level as $item)
                            <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="error-text form-text text-danger"></small>
                </div>

                <!-- General Fields -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                    <small id="error-nama" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Username / NIM</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                    <small id="error-username" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>

                <!-- Additional Fields -->
                <div id="additional-fields">
                    <div id="email-field" class="form-group" style="display: none;">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                        <small id="error-email" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="no-hp-field" class="form-group" style="display: none;">
                        <label>No. HP</label>
                        <input type="number" name="no_hp" id="no_hp" class="form-control">
                        <small id="error-no_hp" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="prodi-field" class="form-group" style="display: none;">
                        <label>Prodi</label>
                        <select name="prodi_id" id="prodi_id" class="form-control">
                            <option value="">- Pilih Prodi -</option>
                            @foreach ($prodi as $item)
                                <option value="{{ $item->prodi_id }}">{{ $item->prodi_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-prodi_id" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="angkatan-field" class="form-group" style="display: none;">
                        <label>Angkatan</label>
                        <input type="number" name="angkatan" id="angkatan" class="form-control">
                        <small id="error-angkatan" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="periode-field" class="form-group" style="display: none;">
                        <label>Periode</label>
                        <select name="periode_id" id="periode_id" class="form-control">
                            <option value="">- Pilih Periode -</option>
                            @foreach ($periode as $item)
                                <option value="{{ $item->periode_id }}">{{ $item->periode_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-periode_id" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {

        // Use delegated event listener to handle dynamically loaded elements
        $(document).on('change', '#level_id', function() {
            var selectedRole = $(this).val();

            // Reset all additional fields
            $('#email-field, #no-hp-field, #prodi-field, #angkatan-field, #periode-field').hide();
            if (selectedRole == '3') {
                $('#email-field, #no-hp-field, #prodi-field, #angkatan-field, #periode-field').show();
            } else if (selectedRole == '4') {
                $('#email-field, #no-hp-field, #prodi-field').show();
            } else if (selectedRole == '2') {
                $('#email-field, #no-hp-field').show();
            } else if (selectedRole == '1') {

            }
        });

        // Trigger the event manually to test
        $('#level_id').trigger('change');

        // Validate form
        $("#form-tambah").validate({
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
                    required: true
                },
                nama: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action, // URL untuk mengirim data
                    type: form.method, // POST method
                    data: $(form).serialize(), // data form yang disubmit
                    success: function(response) {
                        console.log(
                        response); // Log response untuk memeriksa apakah data sudah diterima

                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                dataUser.ajax
                            .reload(); // Reload data setelah berhasil
                            });
                        } else {
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
                    },
                    error: function(xhr, status, error) {
                        console.error(error); // Menampilkan error jika permintaan gagal
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal mengirim data. Coba lagi nanti.'
                        });
                    }

                });
                return false;
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

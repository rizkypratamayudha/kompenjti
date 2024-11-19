@empty($user)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/user') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
<form action="{{ url('/user/' . $user->user_id . '/update_ajax') }}" method="POST" id="form-edit"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Level Pengguna</label>
                    <select name="level_id" id="level_id" class="form-control" required>
                        <option value="">- Pilih Role -</option>
                        @foreach ($level as $l)
                            <option {{ $l->level_id == $user->level_id ? 'selected' : '' }} value="{{ $l->level_id }}">
                                {{ $l->level_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input value="{{ $user->username }}" type="text" name="username" id="username" class="form-control" required>
                    <small id="error-username" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input value="{{ $user->nama }}" type="text" name="nama" id="nama" class="form-control" required>
                    <small id="error-nama" class="error-text form-text text-danger"></small>
                </div>
                @if ($user->level_id != 1)
                    <div class="form-group">
                        <label>Email</label>
                        <input value="{{ isset($contact) ? $contact->email : '' }}" type="text" name="email" id="email" class="form-control" required>
                        <small id="error-email" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>No HP</label>
                        <input value="{{ isset($contact) ? $contact->no_hp : '' }}" type="text" name="no_hp" id="no_hp" class="form-control" required>
                        <small id="error-no_hp" class="error-text form-text text-danger"></small>
                    </div>
                @endif

                <!-- Form untuk level Mahasiswa (3) -->
                @if ($user->level_id == 3)
                    <div class="form-group">
                        <label>Angkatan</label>
                        <input value="{{ isset($contact) ? $contact->angkatan : '' }}" type="text" name="angkatan" id="angkatan" class="form-control" required>
                        <small id="error-angkatan" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Prodi ID</label>
                        <input value="{{ isset($contact) ? $contact->prodi_id : '' }}" type="text" name="prodi_id" id="prodi_id" class="form-control" required>
                        <small id="error-prodi_id" class="error-text form-text text-danger"></small>
                    </div>
                @endif

                <!-- Form untuk level Dosen (2) -->
                @if ($user->level_id == 2)
                    {{-- <div class="form-group">
                        <label>Prodi ID</label>
                        <input value="{{ isset($contact) ? $contact->prodi_id : '' }}" type="text" name="prodi_id" id="prodi_id" class="form-control" required>
                        <small id="error-prodi_id" class="error-text form-text text-danger"></small>
                    </div> --}}
                @endif

               <!-- Form untuk level Kaprodi (4) -->
               @if ($user->level_id == 4)
               <div class="form-group">
                   <label>Prodi</label>
                   <select name="prodi_id" id="prodi_id" class="form-control" required>
                       <option value="">- Pilih Prodi -</option>
                       @foreach ($prodi as $p)
                           <option {{ $p->prodi_id == $contact->prodi_id ? 'selected' : '' }} value="{{ $p->prodi_id }}">
                               {{ $p->prodi_nama }}</option>
                       @endforeach
                   </select>
                   <small id="error-prodi_id" class="error-text form-text text-danger"></small>
               </div>
           @endif

                <!-- Untuk Admin (Level ID = 1), hanya dapat mengubah username, nama, dan password -->
                @if ($user->level_id == 1)
                    <!-- Kolom-kolom di atas sudah cukup untuk Admin -->
                @endif

                <div class="form-group">
                    <label>Password</label>
                    <input value="" type="password" name="password" id="password" class="form-control">
                    <small class="form-text text-muted">Abaikan jika tidak ingin ubah password</small>
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

    <script>
        $(document).ready(function() {
            $("#form-edit").validate({
                rules: {
                    level_id: {
                        required: true,
                        number: true
                    },
                    username: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    nama: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    password: {
                        minlength: 6,
                        maxlength: 20
                    },
                    no_hp: {
                        required: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    email: {
                        required: true,
                        email: true
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                dataUser.ajax.reload();
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
                            console.error(xhr
                            .responseText); // Log error response from server
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
@endempty

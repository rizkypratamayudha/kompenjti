<form action="{{ url('/kompetensi/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kompetensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama :</label>
                    <input value="{{ $user->nama }}" type="text" name="nama" id="nama" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>NIM :</label>
                    <input value="{{ $user->username }}" type="text" name="username" id="username" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>Prodi :</label>
                    <input value="{{ $user->detailMahasiswa->prodi->prodi_nama }}" type="text" name="prodi" id="prodi" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>Periode :</label>
                    <input value="{{ $user->detailMahasiswa->periode->periode_nama }}" type="text" name="periode" id="periode" class="form-control" disabled>
                </div>
            </div>

            <div class="modal-header">
                <h5 class="modal-title">Tambah Detail Kompetensi</h5>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Kompetensi</label>
                    <select name="kompetensi_id" id="kompetensi_id" class="form-control" required>
                        <option value="">- Pilih Kompetensi -</option>
                        @foreach ($kompetensi as $item)
                            <option value="{{$item->kompetensi_admin_id}}">{{$item->kompetensi_nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pengalaman</label>
                    <input type="text" name="pengalaman" id="pengalaman" class="form-control" required>
                    <small id="error-pengalaman" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Bukti</label>
                    <input type="text" name="bukti" id="bukti" class="form-control" required>
                    <small id="error-bukti" class="error-text form-text text-danger"></small>
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
    $(document).ready(function () {
        $("#form-tambah").validate({
            rules: {
                kompetensi_id: {required: true},
                pengalaman: {required: true, minlength: 3, maxlength: 100},
                bukti: {required: true, minlength: 3, maxlength: 100},
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
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'berhasil',
                                text: response.message
                            });
                            dataUser.ajax.reload();
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

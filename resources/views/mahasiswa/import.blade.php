<form action="{{ url('/mahasiswa/import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Import Data Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Download Template</label>
                    <a href="{{ asset('template_mahasiswa.xlsx') }}" class="btn btn-info btn-sm" download>
                        <i class="fa fa-file-excel"></i> Download
                    </a>
                </div>
                <div class="form-group">
                    <label>Pilih File</label>
                    <input type="file" name="file_mahasiswa" id="file_mahasiswa" class="form-control" required>
                    <small id="error-file_mahasiswa" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#form-import").validate({
            rules: {
                file_mahasiswa: {
                    required: true,
                    extension: "xlsx"
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) { // Jika sukses
                            $('#modal-master').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                window.location.reload();
                            });
                        } else { // Jika ada error
                            if (response.errors) { // Tampilkan pesan error detail
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan Data',
                                    html: response.errors // Menampilkan pesan error yang lebih rinci
                                });
                            } else { // Pesan umum
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        }
                    },
                    error: function(xhr) { // Jika server mengembalikan error
                        let errorMessage = xhr.responseJSON.errors || 'Terjadi kesalahan saat memproses file.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Server',
                            html: `<p>${xhr.responseJSON.message}</p><pre>${errorMessage}</pre>`
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

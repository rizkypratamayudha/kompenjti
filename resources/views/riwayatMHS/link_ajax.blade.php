<form action="{{url('riwayat/link')}}" method="POST" id="form-tambah" class="ajax-form">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="progres_id" value="{{$progres->progres_id}}">
                <div class="form-group">
                    <label>Progres : {{$progres->judul_progres}}</label>
                </div>
                <div class="form-group">
                    <label>Nilai Jam Kompen : {{$progres->jam_kompen}}</label>
                </div>
                <div class="form-group">
                    <label>deadlline : {{$progres->deadline ?? '-'}}</label>
                </div>
                <div class="form-group">
                    <label>Link : </label>
                    <input type="url" name="link" id="link" class="form-control" placeholder="Masukkan link..." required>
                    <small id="error-link" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
    $("#form-tambah").validate({
        rules: {
            link: {required: true},
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function(response) {
                    if(response.status){
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // Reload the page after confirming the success message
                            }
                        });
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-'+prefix).text(val[0]);
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
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});

</script>

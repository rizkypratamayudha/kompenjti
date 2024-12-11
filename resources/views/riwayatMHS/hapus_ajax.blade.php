<form action="{{ url('/riwayat/' . $progres->progres_id . '/hapus') }}" method="POST" id="form-delete">
    @csrf
    @method('DELETE')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Batalkan Pengiriman</h5>
                <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                    Apakah Anda ingin membatalkan pengiriman
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">Progres :</th>
                        <td class="col-9">{{ $progres->judul_progres }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nilai Jam Kompen :</th>
                        <td class="col-9">{{ $progres->jam_kompen }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Deadline :</th>
                        <td class="col-9">
                            @if ($progres->deadline)
                                {{ \Carbon\Carbon::parse($progres->deadline)->format('d M Y, H:i') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Pekerjaan :</th>
                        <td class="col-9">
                            @if ($pengumpulan->namaoriginal == null)
                                {{$pengumpulan->bukti_pengumpulan}}
                            @else
                                {{$pengumpulan->namaoriginal}}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#form-delete").validate({
            rules: {},
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
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location
                                .reload(); // Reload the page after confirming the success message
                                }
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

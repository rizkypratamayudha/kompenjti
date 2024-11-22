@empty($kompetensi)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <a href="{{ url('/kompetensi') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
    <form action="{{url('/kompetensi/' .$kompetensi->kompetensi_id. '/update_ajax')}}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Kompetensi</h5>
                    <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kompetensi</label>
                        <select name="kompetensi_id" id="kompetensi_id" class="form-control" required>
                            <option value="">- Pilih Kompetensi</option>
                            @foreach ($kompetensi_admin as $l)
                                <option {{ $l->kompetensi_admin_id == $kompetensi->kompetensi_admin_id ? 'selected' : '' }} value="{{ $l->kompetensi_admin_id }}">
                                    {{ $l->kompetensi_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-level_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Pengalaman</label>
                        <input value="{{$kompetensi->pengalaman}}" type="text" name="pengalaman" id="pengalaman" class="form-control">
                        <small id="error-pengalaman" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Bukti</label>
                        <input value="{{$kompetensi->bukti}}" type="text" name="bukti" id="bukti" class="form-control">
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
        $(document).ready(function() {
            $("#form-edit").validate({
                rules: {
                    kompetensi_id: {
                        required: true,
                    },
                    pengalaman: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    bukti: {
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

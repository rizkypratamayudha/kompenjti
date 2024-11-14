@empty($user)
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
                <a href="{{ url('/user') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data User</h5>
                <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Detail Informasi</h5>
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">User ID :</th>
                        <td class="col-9">{{ $user->user_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Role :</th>
                        <td class="col-9">{{ $user->level->level_nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Username :</th>
                        <td class="col-9">{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nama :</th>
                        <td class="col-9">{{ $user->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Password :</th>
                        <td class="col-9" type="password">*******</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Email :</th>
                        <td class="col-9">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">No HP :</th>
                        <td class="col-9">{{ $user->no_hp }}</td>
                    </tr>
                    @if (!$user->prodi_id && !$user->angkatan && !$user->periode_id)
                    @elseif ($user->prodi_id && $user->angkatan && $user->periode_id)
                        <tr>
                            <th class="text-right col-3">Prodi:</th>
                            <td class="col-9">{{ $user->prodi->prodi_nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Angkatan :</th>
                            <td class="col-9">{{ $user->angkatan }}</td>
                        </tr>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Periode :</th>
                            <td class="col-9">{{ $user->periode->periode_nama }}</td>
                        </tr>
                    @elseif ($user->prodi_id)
                        <tr>
                            <th class="text-right col-3">Prodi:</th>
                            <td class="col-9">{{ $user->prodi->prodi_nama }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger"
                    onclick="declineUser('{{ $user->user_id }}')">Decline</button>
                <button type="button" class="btn btn-success"
                    onclick="approveUser('{{ $user->user_id }}')">Approve</button>
            </div>
        </div>
    </div>
    {{-- alasan --}}
    <div class="modal fade" id="declineReasonModal" tabindex="-1" role="dialog" aria-labelledby="declineReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="declineReasonModalLabel">Decline</h5>
                    <button type="button" class="close" onclick="closeDeclineModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Berikan Alasan</p>
                    <textarea id="declineReason" class="form-control" rows="3" placeholder="Masukkan alasan disini"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger"
                        onclick="submitDeclineReason('{{ $user->user_id }}')">Decline</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approveUser(userId) {
            $.ajax({
                url: 'validasi/approve/' + userId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#myModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'User Approved',
                        text: response.message
                    });
                    dataUser.ajax.reload();
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Approval Failed',
                        text: response.responseJSON.error
                    });
                }
            });
        }

        function declineUser(userId) {
            $('#declineReasonModal').modal('show');
        }

        function closeDeclineModal() {
            $('#declineReasonModal').modal('hide');
        }

        function submitDeclineReason(userId) {
            const reason = $('#declineReason').val();

            $.ajax({
                url: 'validasi/decline/' + userId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(response) {
                    $('#declineReasonModal').modal('hide'); // Tutup modal alasan
                    $('#myModal').modal('hide'); // Tutup modal utama

                    Swal.fire({
                        icon: 'success',
                        title: 'User Declined',
                        text: response.message
                    });

                    dataUser.ajax.reload();
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Decline Failed',
                        text: response.responseJSON.error
                    });
                }
            });
        }


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

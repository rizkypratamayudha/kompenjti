@empty($pekerjaan)
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
                <a href="{{ url('/dosen') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <input name="pekerjaan_id" value="{{ $pekerjaan->pekerjaan_id }}" hidden>
    <input name="user_id" value="{{ Auth::id() }}" hidden>
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Pekerjaan</h5>
                <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Detail Informasi Pekerjaan</h5>
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">Nama Dosen :</th>
                        <td class="col-9">{{ $pekerjaan->user->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nomor HP Dosen :</th>
                        <td class="col-9">{{ $pekerjaan->user->detailDosen->no_hp }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jenis Pekerjaan :</th>
                        <td class="col-9">{{ $pekerjaan->jenis_pekerjaan }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nama Pekerjaan :</th>
                        <td class="col-9">{{ $pekerjaan->pekerjaan_nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jumlah Progres :</th>
                        <td class="col-9">{{ $jumlahProgres }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nilai Total Jam Kompen :</th>
                        <td class="col-9">{{ $pekerjaan->jumlah_jam_kompen }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jumlah Anggota :</th>
                        <td class="col-9">{{ $detailPekerjaan ? $detailPekerjaan->jumlah_anggota : 'Tidak tersedia' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Persyaratan :</th>
                        <td class="col-9">
                            <ul>
                                @foreach ($persyaratan as $item)
                                    <li>{{ $item->persyaratan_nama }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Kompetensi :</th>
                        <td class="col-9">
                            <ul>
                                @foreach ($kompetensi as $kompetensiDosen)
                                    <li>{{ $kompetensiDosen->kompetensiAdmin->kompetensi_nama }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <th class="text-right col-3">Deskripsi Tugas :</th>
                        <td class="col-9">
                            @if($pekerjaan->detail_pekerjaan->isNotEmpty())
                                <ul>
                                    @foreach ($pekerjaan->detail_pekerjaan as $detail)
                                        <li>{{ $detail->deskripsi_tugas }}</li>
                                    @endforeach
                                </ul>
                            @else
                                Tidak tersedia
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Tutup</button>
            </div>
        </div>
    </div>
@endempty
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

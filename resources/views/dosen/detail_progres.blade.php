@empty($pengumpulan)
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
                <a href="{{ url('/enter-progres') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@endempty
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Pengumpulan</h5>
            <button type="button" class="close" data-dismiss="modal" arialabel="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> Detail Informasi Pengumpulan</h5>
            </div>
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th class="text-right col-3">Nama Mahasiswa :</th>
                    <td class="col-9">{{ $pengumpulan->user->nama }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">NIM Mahasiswa :</th>
                    <td class="col-9">{{ $pengumpulan->user->username }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Status Pengumpulan :</th>
                    <td class="col-9">
                        @if ($pengumpulan->status == 'pending')
                            Sudah Diserahkan
                        @elseif($pengumpulan->status =='accept')
                            Sudah Dinilai : {{$pengumpulan->progres->jam_kompen}}
                        @else
                            Sudah Dinilai : 0 
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="text-right col-3">Bukti pengumpulan :</th>
                    <td class="col-9">
                        @if ($pengumpulan && $pengumpulan->bukti_pengumpulan)
                            @if (str_starts_with($pengumpulan->bukti_pengumpulan, 'https://'))
                                <!-- If the bukti_pengumpulan is a URL that starts with 'https://' -->
                                <a class="text-decoration-none" href="{{ $pengumpulan->bukti_pengumpulan }}">
                                    {{ $pengumpulan->bukti_pengumpulan }}
                                </a>
                            @elseif(str_starts_with($pengumpulan->bukti_pengumpulan, 'pengumpulan_gambar/'))
                                <!-- If bukti_pengumpulan is an image stored in 'pengumpulan_gambar/' -->
                                <img class="text-decoration-none mt-3"
                                    src="{{ asset('storage/' . $pengumpulan->bukti_pengumpulan) }}"
                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            @elseif(str_starts_with($pengumpulan->bukti_pengumpulan, 'pengumpulan_file/'))
                                <!-- If bukti_pengumpulan is a file stored in 'pengumpulan_file/' -->
                                @php
                                    // Extract the original file name by getting the last part of the path
                                    $filePath = storage_path('app/public/' . $pengumpulan->bukti_pengumpulan);
                                    $fileName = $pengumpulan->namaoriginal;
                                @endphp
                                <i class="fa fa-file-pdf" style="color: red"></i>
                                <a class="text-decoration-none mt-3"
                                    href="{{ asset('storage/' . $pengumpulan->bukti_pengumpulan) }}"
                                    download="{{ $fileName }}">
                                    {{ $fileName }}

                                </a>
                            @else
                                <!-- If bukti_pengumpulan does not match any of the conditions above, display '-' -->
                                <span>-</span>
                            @endif
                        @else
                            <!-- If $pengumpulan or bukti_pengumpulan is not found -->
                            <span>-</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            @if ($pengumpulan->status == 'pending')
                <button type="button" class="btn btn-success"
                    onclick="approveTugas('{{ $pengumpulan->pengumpulan_id }}')">
                    Accept
                </button>
                <button type="button" class="btn btn-danger"
                    onclick="declineTugas('{{ $pengumpulan->pengumpulan_id }}')">
                    Decline
                </button>
            @endif
        </div>
    </div>
</div>

<script>
    function approveTugas(pengumpulanId) {
        $.ajax({
            url: '{{ url('dosen/approve') }}/' + pengumpulanId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#myModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Tugas Disetujui',
                    text: response.message
                });
                dataUser.ajax.reload();
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tugas Disetujui gagal',
                    text: response.responseJSON.error
                });
            }
        });
    }

    function declineTugas(pengumpulanId) {
        $.ajax({
            url: '{{ url('dosen/decline') }}/' + pengumpulanId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#myModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Tugas Ditolak dan bernilai 0',
                    text: response.message
                });
                dataUser.ajax.reload();
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tugas Disetujui gagal',
                    text: response.responseJSON.error
                });
            }
        });
    }
</script>

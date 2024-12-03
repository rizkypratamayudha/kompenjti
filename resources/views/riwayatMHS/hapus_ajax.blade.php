<form action="{{url('/riwayat/' . $progres->progres_id . '/hapus')}}" method="POST" id="form-delete">
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
                        <td class="col-9">{{$progres->judul_progres}}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nilai Jam Kompen :</th>
                        <td class="col-9">{{$progres->jam_kompen}}</td>
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
                        <td class="col-9">{{$pengumpulan->bukti_pengumpulan}}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</form>

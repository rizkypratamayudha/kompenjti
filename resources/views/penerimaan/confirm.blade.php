@empty($penerimaan)
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
                    <td class="col-9">{{ $penerimaan->user->nama }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">NIM Mahasiswa :</th>
                    <td class="col-9">{{ $penerimaan->user->username }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Pekerjaan :</th>
                    <td class="col-9">{{ $penerimaan->pekerjaan->pekerjaan_nama }}</td>
                </tr>
                <tr>
                    <th class="text-right col-3">Nama Dosen :</th>
                    <td class="col-9">{{ $penerimaan->pekerjaan->user->nama }}</td>
                </tr>
                </tr>
                @foreach ($pengumpulan as $item)
                <tr>
                    <th class="text-right col-3">Pengumpulan Progres :</th>
                    <td class="col-9">{{ $item->bukti_pengumpulan }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="modal-footer">

        </div>
    </div>
</div>

<script>


</script>

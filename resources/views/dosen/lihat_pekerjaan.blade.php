<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
                @empty($user)
                    Kesalahan
                @else
                    Detail Data Penjualan
                @endempty
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            @empty($user)
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/pekerjaan') }}" class="btn btn-warning">Kembali</a>
            @else
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Detail Kompetensi</h5>
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">Nama :</th>
                        <td class="col-9">{{ $user->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">NIM :</th>
                        <td class="col-9">{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Prodi :</th>
                        <td class="col-9">{{ $user->detailMahasiswa->prodi->prodi_nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Email :</th>
                        <td class="col-9">{{ $user->detailMahasiswa->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">NO HP :</th>
                        <td class="col-9">{{ $user->detailMahasiswa->no_hp }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Periode :</th>
                        <td class="col-9">{{ $user->detailMahasiswa->periode->periode_nama  }}</td>
                    </tr>
                </table>

                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Detail Informasi Kompetensi</h5>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Kompetensi</th>
                            <th>Pengalaman</th>
                            <th>Bukti</th>\
                        </tr>
                    </thead>
                    @foreach ($kompetensi as $detail)
                        <tbody>
                            <tr>
                                <td class="col-9">{{ $detail->kompetensiAdmin->kompetensi_nama }}</td>
                                <td class="col-9">{{ $detail->pengalaman }}</td>
                                <td class="col-9">{{ $detail->bukti }}</td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            @endempty
        </div>
    </div>
</div>

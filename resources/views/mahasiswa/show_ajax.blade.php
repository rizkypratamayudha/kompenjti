
@empty($jamKompen)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/mahasiswa/') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Informasi !!!</h5>
                    Berikut adalah detail data Mahasiswa:
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">NIM :</th>
                        <td class="col-9">{{ $jamKompen->user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nama :</th>
                        <td class="col-9">{{ $jamKompen->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Periode :</th>
                        <td class="col-9">{{ $jamKompen->periode->periode_nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Akumulasi Jam :</th>
                        <td class="col-9">{{ $jamKompen->akumulasi_jam }}</td>
                    </tr>


                </table>
                <div class="alert" style="background-color: #9BC4E2; color: #fff; border: 1px solid #7FAFC8;">
                    <h5><i class="icon fas fa-list"></i> Detail</h5>
                </div>                
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>Jumlah Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp 
                        @foreach ($jamKompen->detail_jamKompen as $detail)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $detail->matkul->matkul_nama }}</td>
                                <td>{{ $detail->jumlah_jam }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    

                </table>
            </div>
        </div>
    </div>
@endempty

<form action="{{ url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/delete_ajax') }}" method="POST"
    id="form-delete-mahasiswa">
    @csrf
    @method('DELETE')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Data Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Konfirmasi !!!</h5>
                    Apakah Anda ingin menghapus data berikut?
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

                {{-- <div class="alert" style="background-color: #9BC4E2; color: #fff; border: 1px solid #7FAFC8;">
                    <h5><i class="icon fas fa-list"></i> Detail Barang</h5>
                </div>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($penjualanDetail as $detail)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $detail->barang->barang_nama }}</td>
                                <td>{{ $detail->harga }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>{{ number_format($detail->harga * $detail->jumlah) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table> --}}
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Event submit form
        $("#form-delete-mahasiswa").on('submit', function(event) {
            event.preventDefault(); // Mencegah reload halaman
            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        $('#modal-master').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            dataMahasiswa.ajax.reload();
                        });

                        // Cek apakah instance DataTables sudah ada
                        if ($.fn.DataTable.isDataTable('#dataMahasiswa')) {
                            // Reload dan refresh tabel penjualan
                            $('#dataMahasiswa').DataTable().ajax.reload(null, false).draw();
                        }
                    } else {
                        // Tampilkan pesan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal menghapus data. Silakan coba lagi.'
                    });
                }
            });
        });
    });
</script>

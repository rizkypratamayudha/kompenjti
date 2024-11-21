<form action="{{ url('/kompetensi/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kompetensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama :</label>
                    <input value="{{ $user->nama }}" type="text" name="nama" id="nama" class="form-control"
                        disabled>
                    <small id="error-nama" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>NIM :</label>
                    <input value="{{ $user->username }}" type="text" name="username" id="username"
                        class="form-control" disabled>
                    <small id="error-username" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Prodi :</label>
                    <input value="{{ $user->detailMahasiswa->prodi->prodi_nama }}" type="text" name="prodi"
                        id="prodi" class="form-control" disabled>
                    <small id="error-prodi" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Periode :</label>
                    <input value="{{ $user->detailMahasiswa->periode->periode_nama }}" type="text" name="prodi"
                        id="prodi" class="form-control" disabled>
                    <small id="error-periode" class="error-text form-text text-danger"></small>
                </div>
            </div>

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Detail Kompetensi</h5>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Jumlah Kompetensi</label>
                    <input type="number" name="jumlah_kompetensi" id="jumlah_kompetensi" class="form-control"
                        min="1" required>
                    <small id="error-jumlah_kompetensi" class="error-text form-text text-danger"></small>
                </div>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-responsive" id="table-kompetensi">
                    <thead>
                        <tr>
                            <th>
                                Nama Kompetensi
                            </th>
                            <th>Pengalaman</th>
                            <th>Bukti</th>
                        </tr>
                    </thead>
                    <tbody id="dynamic-inputs"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Event untuk memproses perubahan jumlah kompetensi
        $('#jumlah_kompetensi').on('change', function () {
            const jumlahKompetensi = parseInt($(this).val());
            const $dynamicInputs = $('#dynamic-inputs');

            // Validasi input jumlah kompetensi
            if (isNaN(jumlahKompetensi) || jumlahKompetensi <= 0) {
                $('#error-jumlah_kompetensi').text('Jumlah kompetensi harus lebih dari 0');
                return;
            }

            // Bersihkan error
            $('#error-jumlah_kompetensi').text('');
            $dynamicInputs.empty();

            // Tambahkan baris dinamis sesuai jumlah kompetensi
            for (let i = 1; i <= jumlahKompetensi; i++) {
                $dynamicInputs.append(`
                    <tr id="row-${i}">
                        <td>
                            <select name="kompetensi[${i}][nama]" class="form-control"  required>
                                <option>- Pilih Kompetensi -</option>
                                @foreach ($kompetensi as $item)
                                    <option value="{{$item->kompetensi_admin_id}}">{{$item->kompetensi_nama}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="kompetensi[${i}][pengalaman]" class="form-control" placeholder="Pengalaman" required>
                            <small class="error-text text-danger" id="error-pengalaman-${i}"></small>
                        </td>
                        <td>
                            <input type="text" name="kompetensi[${i}][bukti]" class="form-control" placeholder="Bukti" required>
                            <small class="error-text text-danger" id="error-bukti-${i}"></small>
                        </td>
                    </tr>
                `);
            }
        });

        // Validasi sebelum submit form
        $('#form-tambah').on('submit', function (e) {
            e.preventDefault(); // Prevent form submission for AJAX processing
            let isValid = true;

            // Validasi setiap input kompetensi
            $('#dynamic-inputs').find('tr').each(function () {
                const nama = $(this).find('input[name*="[nama]"]').val();
                const pengalaman = $(this).find('input[name*="[pengalaman]"]').val();
                const bukti = $(this).find('input[name*="[bukti]"]').val();

                if (!nama) {
                    $(this).find('.error-text[id^="error-nama"]').text('Nama kompetensi wajib diisi');
                    isValid = false;
                } else {
                    $(this).find('.error-text[id^="error-nama"]').text('');
                }

                if (!pengalaman) {
                    $(this).find('.error-text[id^="error-pengalaman"]').text('Pengalaman wajib diisi');
                    isValid = false;
                } else {
                    $(this).find('.error-text[id^="error-pengalaman"]').text('');
                }

                if (!bukti) {
                    $(this).find('.error-text[id^="error-bukti"]').text('Bukti wajib diunggah');
                    isValid = false;
                } else {
                    $(this).find('.error-text[id^="error-bukti"]').text('');
                }
            });

            // Jika validasi gagal
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Harap periksa kembali semua input!'
                });
                return;
            }

            // Jika validasi berhasil, kirim data melalui AJAX
            $.ajax({
                url: $('#form-tambah').attr('action'),
                method: 'POST',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                        }).then(() => {
                            dataUser.ajax.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server!',
                    });
                }
            });
        });
    });
</script>

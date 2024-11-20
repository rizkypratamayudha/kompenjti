<form action="{{ url('/mahasiswa/ajax') }}" method="POST" id="form-tambah-mahasiswa">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Mahasiswa Kompensasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIM</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">- Pilih Mahasiswa -</option>
                        @foreach($user as $u)
                            <option value="{{ $u->user_id }}" data-nama="{{ $u->nama }}">{{ $u->username }}</option>
                        @endforeach
                    </select>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Periode</label>
                    <select name="periode_id" id="periode_id" class="form-control" required>
                        <option value="">- Pilih Periode -</option>
                        @foreach($periode as $p)
                            <option value="{{ $p->periode_id }}">{{ $p->periode_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-periode_id" class="error-text form-text text-danger"></small>
                </div>


                <!-- Dynamic table for mata kuliah input -->
                <div class="form-group">
                    <label>Mata Kuliah</label>
                    <div class="d-flex align-items-center mb-2">
                        <button type="button" id="add-row" class="btn btn-info btn-sm">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <table class="table table-bordered" id="table-mata-kuliah">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Kuliah</th>
                                <th>Jumlah Jam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mata-kuliah-rows">
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>


                <div class="form-group">
                    <label>Akumulasi Jam Kompensasi</label>
                    <input type="number" name="akumulasi_jam" id="akumulasi_jam" class="form-control" readonly>
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
$(document).ready(function () {
    let rowCount = 0;

    // Mengisi nama berdasarkan pilihan NIM
    $('#user_id').on('change', function () {
        let nama = $(this).find(':selected').data('nama');
        $('#nama').val(nama || '');
    });

    // Tambah baris baru pada tabel mata kuliah
    $('#add-row').on('click', function () {
        rowCount++;
        const newRow = `
            <tr>
                <td class="row-number"></td>
                <td>
                    <select name="matkul_id[]" class="form-control mata-kuliah-select" required>
                        <option value="">- Pilih Mata Kuliah -</option>
                        @foreach($matkul as $m)
                            <option value="{{ $m->matkul_id }}">{{ $m->matkul_nama }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="jumlah_jam[]" class="form-control jumlah-jam" min="1" required></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
            </tr>`;
        $('#mata-kuliah-rows').append(newRow);
        updateRowNumbers(); // Perbarui nomor baris
        updateAkumulasiJam(); // Perbarui akumulasi jam
    });

    // Hapus baris pada tabel mata kuliah
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove(); // Hapus baris terkait
        updateRowNumbers(); // Perbarui nomor baris setelah penghapusan
        updateAkumulasiJam(); // Perbarui akumulasi jam
    });

    // Update akumulasi jam setiap kali input jumlah jam berubah
    $(document).on('input', '.jumlah-jam', function () {
        updateAkumulasiJam();
    });

    // Fungsi untuk memperbarui nomor baris
    function updateRowNumbers() {
        $('#mata-kuliah-rows tr').each(function (index) {
            $(this).find('.row-number').text(index + 1); // Perbarui nomor kolom "No"
        });
    }

    // Fungsi untuk menghitung total jumlah jam
    function updateAkumulasiJam() {
        let totalJam = 0;
        $('.jumlah-jam').each(function () {
            const val = parseInt($(this).val());
            if (!isNaN(val)) totalJam += val;
        });
        $('#akumulasi_jam').val(totalJam); // Update input akumulasi jam
    }

    // Submit form menggunakan Ajax
    $("#form-tambah-mahasiswa").validate({
        rules: {
            user_id: { required: true, number: true }, // Validasi untuk NIM / user_id
            periode_id: { required: true, number: true }, // Validasi untuk periode_id
            'matkul_id[]': { required: true }, // Validasi untuk mata kuliah
            'jumlah_jam[]': { required: true, number: true } // Validasi untuk jumlah jam
        },
        submitHandler: function (form) {
            // Form submit using AJAX
            $.ajax({
                url: form.action,
                method: form.method,
                data: $(form).serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        $('#modal-master').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        dataMahasiswa.ajax.reload();
                        form.reset();
                        $('#mata-kuliah-rows').empty();
                        updateAkumulasiJam();
                    } else {
                        $('.error-text').text(''); // Hapus pesan error sebelumnya
                        $.each(response.errors, function (field, messages) {
                            $('#error-' + field).text(messages[0]); // Tampilkan pesan error
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Silakan coba lagi.'
                    });
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element) {
            $(element).addClass('is-invalid'); // Menandai input yang salah
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid'); // Menghapus tanda kesalahan
        }
    });

});
</script>

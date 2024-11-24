@empty($jamKompen)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/mahasiswa') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/mahasiswa/' . $jamKompen->jam_kompen_id . '/update_ajax') }}" method="POST" id="form-edit-mahasiswa">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Mahasiswa Kompensasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIM</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">- Pilih Mahasiswa -</option>
                            @foreach($user as $u)
                                <option value="{{ $u->user_id }}" data-nama="{{ $u->nama }}" 
                                    {{ $jamKompen->user_id == $u->user_id ? 'selected' : '' }}>
                                    {{ $u->username }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-user_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ $jamKompen->user->nama }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Periode</label>
                        <select name="periode_id" id="periode_id" class="form-control" required>
                            <option value="">- Pilih Periode -</option>
                            @foreach($periode as $p)
                                <option value="{{ $p->periode_id }}" {{ $jamKompen->periode_id == $p->periode_id ? 'selected' : '' }}>
                                    {{ $p->periode_nama }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-periode_id" class="error-text form-text text-danger"></small>
                    </div>
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
                                @foreach($jamKompen->details as $index => $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <select name="matkul_id[]" class="form-control mata-kuliah-select" required>
                                                <option value="">- Pilih Mata Kuliah -</option>
                                                @foreach($matkul as $m)
                                                    <option value="{{ $m->matkul_id }}" 
                                                        {{ $detail->matkul_id == $m->matkul_id ? 'selected' : '' }}>
                                                        {{ $m->matkul_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="jumlah_jam[]" class="form-control jumlah-jam" value="{{ $detail->jumlah_jam }}" required></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <label>Akumulasi Jam Kompensasi</label>
                        <input type="number" name="akumulasi_jam" id="akumulasi_jam" class="form-control" value="{{ $jamKompen->akumulasi_jam }}" readonly>
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
            $(document).ready(function () {
    // Menyimpan referensi elemen penting
    const $mataKuliahRows = $("#mata-kuliah-rows");
    const $akumulasiJam = $("#akumulasi_jam");

    // Fungsi untuk menghitung total akumulasi jam
    function hitungAkumulasiJam() {
        let totalJam = 0;
        $(".jumlah-jam").each(function () {
            const jam = parseInt($(this).val());
            if (!isNaN(jam)) {
                totalJam += jam;
            }
        });
        $akumulasiJam.val(totalJam);
    }

    // Tambah baris mata kuliah
    $("#add-row").on("click", function () {
        const rowCount = $mataKuliahRows.find("tr").length + 1;
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>
                    <select name="matkul_id[]" class="form-control mata-kuliah-select" required>
                        <option value="">- Pilih Mata Kuliah -</option>
                        ${$("#matkul-template").html()}
                    </select>
                </td>
                <td>
                    <input type="number" name="jumlah_jam[]" class="form-control jumlah-jam" min="1" value="0" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                </td>
            </tr>`;
        $mataKuliahRows.append(newRow);
    });

    // Hapus baris mata kuliah
    $mataKuliahRows.on("click", ".remove-row", function () {
        $(this).closest("tr").remove();
        hitungAkumulasiJam(); // Hitung ulang akumulasi setelah baris dihapus
    });

    // Hitung ulang akumulasi saat jumlah jam diubah
    $mataKuliahRows.on("input", ".jumlah-jam", function () {
        hitungAkumulasiJam();
    });

    // Autofill nama mahasiswa berdasarkan NIM yang dipilih
    $("#user_id").on("change", function () {
        const nama = $(this).find("option:selected").data("nama");
        $("#nama").val(nama);
    });

    // Validasi dan pengiriman form menggunakan AJAX
    $("#form-edit-mahasiswa").validate({
        rules: {
            user_id: { required: true },
            periode_id: { required: true },
            "matkul_id[]": { required: true },
            "jumlah_jam[]": { required: true, min: 1 },
        },
        submitHandler: function (form) {
            const $form = $(form);
            $.ajax({
                url: $form.attr("action"),
                type: $form.attr("method"),
                data: $form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $("#myModal").modal("hide");
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: response.message,
                        });
                        // Reload tabel atau data setelah sukses
                        dataMahasiswa.ajax.reload();
                    } else {
                        $(".error-text").text("");
                        if (response.errors) {
                            $.each(response.errors, function (key, val) {
                                $(`#error-${key}`).text(val[0]);
                            });
                        }
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: response.message,
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Kesalahan Server",
                        text: "Terjadi kesalahan saat memproses permintaan.",
                    });
                },
            });
            return false;
        },
        errorElement: "span",
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
    });

    // Hitung akumulasi jam saat halaman dimuat
    hitungAkumulasiJam();
});

        });
    </script>
@endempty

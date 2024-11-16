<form action="{{ url('/dosen/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Pekerjaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Jenis Pekerjaan</label>
                    <select name="jenis_pekerjaan" id="jenis_pekerjaan" class="form-control" required>
                        <option value="">- Pilih Jenis -</option>
                        <option value="Teknis">Teknis</option>
                        <option value="Pengabdian">Pengabdian</option>
                        <option value="Penelitian">Penelitian</option>
                    </select>
                    <small id="error-jenis_pekerjaan" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama Pekerjaan</label>
                    <input value="" type="text" name="pekerjaan_nama" id="pekerjaan_nama" class="form-control"
                        required>
                    <small id="error-pekerjaan_nama" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Jumlah Anggota</label>
                    <input value="" type="number" name="jumlah_anggota" id="jumlah_anggota" class="form-control"
                        required>
                    <small id="error-jumlah_anggota" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Persyaratan</label>
                    <div id="tag-container" class="form-control"
                        style="display: flex; flex-wrap: wrap; min-height: 38px;">
                        <input type="text" id="tag-input" class="tag-input" placeholder="Tambah persyaratan"
                            style="border: none; outline: none; flex-grow: 1;">
                    </div>
                    <small id="error-password" class="error-text form-text text-danger"></small>
                    <input type="hidden" name="persyaratan" id="persyaratan-hidden">
                </div>
                <div class="form-group">
                    <label>Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi_tugas" id="deskripsi_tugas" cols="30" rows="5" class="form-control"></textarea>
                    <small id="error-deskripsi_tugas" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Jumlah Progres</label>
                    <input value="" type="number" name="jumlah_progres" id="jumlah_progres" class="form-control"
                        required>
                    <small id="error-jumlah_progres" class="error-text form-text text-danger"></small>
                </div>
                <input type="text" value="open" name="status" id="status" hidden>
                <div class="modal-body">
                    <table class="table table-bordered" id="table-progres">
                        <thead>
                            <tr>
                                <th>Judul Progres</th>
                                <th>Nilai Jam Kompen</th>
                                <th>Hari</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic-inputs"></tbody>
                    </table>
                    <div class="text-right mr-10">
                        <strong>Total Nilai Jam Kompen: </strong> <span id="total-nilai">0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<style>
    .tag {
        display: inline-block;
        padding: 5px 10px;
        margin: 3px;
        background-color: #007bff;
        color: white;
        border-radius: 3px;
    }

    .tag .remove-tag {
        margin-left: 5px;
        cursor: pointer;
        color: #ffffff;
    }
</style>

<script>
    $(document).ready(function() {
        // Event untuk input jumlah_progres
        $('#jumlah_progres').on('input', function() {
            var count = $(this).val();
            generateProgressFields(count);
        });

        // Fungsi untuk membuat input dinamis di table-progres berdasarkan jumlah_progres
        function generateProgressFields(count) {
            $('#dynamic-inputs').empty();

            for (var i = 0; i < count; i++) {
                var row = `
                <tr>
                    <td>
                        <input type="text" name="judul_progres[]" class="form-control" placeholder="Judul Progres" required>
                    </td>
                    <td>
                        <input type="number" name="jam_kompen[]" class="form-control nilai-jam-kompen" placeholder="Nilai Jam Kompen" required>
                    </td>
                    <td>
                        <input type="text" name="hari[]" class="form-control" placeholder="Hari" required>
                    </td>
                </tr>
                `;
                $('#dynamic-inputs').append(row);
            }

            attachInputEvent();
        }

        // Fungsi untuk menghitung total nilai jam kompen
        function calculateTotalNilai() {
            var totalNilai = 0;
            $('input[name="jam_kompen[]"]').each(function() {
                var nilai = parseFloat($(this).val()) || 0;
                totalNilai += nilai;
            });
            $('#total-nilai').text(totalNilai);
        }

        // Attach event pada setiap input jam kompen untuk menghitung total
        function attachInputEvent() {
            $('.nilai-jam-kompen').off('input').on('input', function() {
                calculateTotalNilai();
            });
        }

        // Validasi dan Ajax Submit
        $("#form-tambah").validate({
            rules: {
                // Tambahkan aturan validasi lainnya sesuai kebutuhan
            },
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
                        } else {
                            $('.error-text').text('');
                            $.each(response.errors, function(prefix, val) {
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
            }
        });

        // Tag input untuk persyaratan
        const tagInput = document.getElementById("tag-input");
        const tagContainer = document.getElementById("tag-container");

        tagInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter" && tagInput.value.trim() !== "") {
                event.preventDefault();
                addTag(tagInput.value.trim());
                tagInput.value = "";
            }
        });

        function addTag(text) {
            const tag = document.createElement("span");
            tag.classList.add("tag");
            tag.innerText = text;

            const removeIcon = document.createElement("span");
            removeIcon.classList.add("remove-tag");
            removeIcon.innerHTML = "&times;";
            removeIcon.onclick = () => {
                tagContainer.removeChild(tag);
                updatePersyaratanInput();
            };

            tag.appendChild(removeIcon);
            tagContainer.insertBefore(tag, tagInput);
            updatePersyaratanInput();
        }

        function updatePersyaratanInput() {
            let tags = [];
            document.querySelectorAll('#tag-container .tag').forEach(tag => {
                tags.push(tag.innerText.replace("×", "").trim());
            });
            document.getElementById('persyaratan-hidden').value = JSON.stringify(tags); // Ubah array ke JSON
        }

    });
</script>

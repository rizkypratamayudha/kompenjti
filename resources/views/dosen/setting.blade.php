<form action="{{ url('/dosen/' . $pekerjaan->pekerjaan_id . '/update_ajax') }}" method="POST" id="form-tambah">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Setting Pekerjaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="status" id="status-input" value="{{ $pekerjaan->status }}">
                <!-- Status Pekerjaan -->
                <div class="form-group">
                    <label>Status Pekerjaan</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status_pekerjaan" name="status"
                            value="open" {{ $pekerjaan->status == 'open' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_pekerjaan" id="status-label">
                            {{ $pekerjaan->status == 'open' ? 'open' : 'close' }}
                        </label>
                    </div>
                </div>


                <!-- Jenis Pekerjaan -->
                <div class="form-group">
                    <label>Jenis Pekerjaan</label>
                    <select name="jenis_pekerjaan" id="jenis_pekerjaan" class="form-control" required>
                        <option value="">- Pilih Jenis -</option>
                        <option value="Teknis" {{ $pekerjaan->jenis_pekerjaan == 'teknis' ? 'selected' : '' }}>Teknis
                        </option>
                        <option value="Pengabdian" {{ $pekerjaan->jenis_pekerjaan == 'pengabdian' ? 'selected' : '' }}>
                            Pengabdian</option>
                        <option value="Penelitian" {{ $pekerjaan->jenis_pekerjaan == 'penelitian' ? 'selected' : '' }}>
                            Penelitian</option>
                    </select>
                </div>

                <!-- Nama Pekerjaan -->
                <div class="form-group">
                    <label>Nama Pekerjaan</label>
                    <input value="{{ $pekerjaan->pekerjaan_nama }}" type="text" name="pekerjaan_nama"
                        id="pekerjaan_nama" class="form-control" required>
                </div>

                <!-- Jumlah Anggota -->
                <div class="form-group">
                    <label>Jumlah Anggota</label>
                    <input value="{{ $pekerjaan->detail_pekerjaan->jumlah_anggota ?? '' }}" type="number"
                        name="jumlah_anggota" id="jumlah_anggota" class="form-control" required>
                </div>


                <div class="form-group">
                    <label>Persyaratan</label>
                    <div id="tag-container" class="form-control"
                        style="display: flex; flex-wrap: wrap; min-height: 38px;">
                        @foreach ($pekerjaan->detail_pekerjaan->persyaratan as $persyaratan)
                            <span class="tag">{{ $persyaratan->persyaratan_nama }}
                                <span class="remove-tag" onclick="removeTag(this)">×</span>
                            </span>
                        @endforeach
                        <input type="text" id="tag-input" class="tag-input" placeholder="Tambah persyaratan"
                            style="border: none; outline: none; flex-grow: 1;">
                    </div>
                    <input type="hidden" name="persyaratan" id="persyaratan-hidden"
                        value="{{ json_encode($pekerjaan->detail_pekerjaan->persyaratan->pluck('persyaratan_nama')->toArray()) }}">
                </div>

                <div class="form-group">
                    <label>Kompetensi</label>
                    <select name="kompetensi_id[]" id="kompetensi_id" class="form-control select2" multiple required>
                        @foreach ($kompetensi as $item)
                            <option value="{{ $item->kompetensi_admin_id }}">{{ $item->kompetensi_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-kompetensi_id" class="error-text form-text text-danger"></small>
                </div>
                <!-- Deskripsi Tugas -->
                <div class="form-group">
                    <label>Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi_tugas" id="deskripsi_tugas" cols="30" rows="5" class="form-control">{{ $pekerjaan->detail_pekerjaan->deskripsi_tugas ?? '' }}</textarea>
                </div>

                <!-- Jumlah Progres -->
                <div class="form-group">
                    <label>Jumlah Progres</label>
                    <input value="{{ $pekerjaan->progres->count() }}" type="number" name="jumlah_progres"
                        id="jumlah_progres" class="form-control" required readonly>
                </div>

                <!-- Tabel Progres -->
                <div class="modal-body">
                    <table class="table table-bordered" id="table-progres">
                        <thead>
                            <tr>
                                <th>Judul Progres</th>
                                <th>Nilai Jam Kompen</th>
                                <th>Hari</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic-inputs">
                            @foreach ($pekerjaan->progres as $progres)
                                <tr>
                                    <td><input type="text" name="judul_progres[]"
                                            value="{{ $progres->judul_progres }}" class="form-control"
                                            placeholder="Judul Progres" required></td>
                                    <td><input type="number" name="jam_kompen[]" value="{{ $progres->jam_kompen }}"
                                            class="form-control nilai-jam-kompen" placeholder="Nilai Jam Kompen"
                                            required></td>
                                    <td><input type="text" name="hari[]" value="{{ $progres->hari }}"
                                            class="form-control" placeholder="Hari" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-right mr-10">
                        <strong>Total Nilai Jam Kompen: </strong> <span id="total-nilai">0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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

    .select2-container .select2-selection--multiple {
        min-height: 38px; /* Sama dengan tinggi elemen form-control */
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 6px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border: 1px solid #0056b3;
        color: #ffffff;
        border-radius: 2px;
        padding: 3px 10px;
    }
</style>
<script>
    $(document).ready(function() {

        $('#kompetensi_id').select2({
            placeholder: "Pilih Kompetensi",
            allowClear: true,
            width: '100%'
        });

        // Handle perubahan status pekerjaan
        $('#status_pekerjaan').on('change', function() {
            var status = $(this).prop('checked') ? 'open' : 'close';
            $('#status-label').text(status.charAt(0).toUpperCase() + status.slice(1));
            $('#status-input').val(status);
        });

        // Handle jumlah progres
        $('#jumlah_progres').on('input', function() {
            var count = $(this).val();
            generateProgressFields(count);
        });

        // Fungsi untuk menambahkan input progres berdasarkan jumlah progres
        function generateProgressFields(count) {
            $('#dynamic-inputs').empty();
            var judulProgres = @json($pekerjaan->progres->pluck('judul_progres')->toArray());
            var jamKompen = @json($pekerjaan->progres->pluck('jam_kompen')->toArray());
            var hari = @json($pekerjaan->progres->pluck('hari')->toArray());

            for (var i = 0; i < count; i++) {
                var judulValue = judulProgres[i] || '';
                var jamKompenValue = jamKompen[i] || '';
                var hariValue = hari[i] || '';

                var row = `
                <tr>
                    <td><input type="text" name="judul_progres[]" class="form-control" placeholder="Judul Progres" value="${judulValue}" required readonly></td>
                    <td><input type="number" name="jam_kompen[]" class="form-control nilai-jam-kompen" placeholder="Nilai Jam Kompen" value="${jamKompenValue}" required></td>
                    <td><input type="text" name="hari[]" class="form-control" placeholder="Hari" value="${hariValue}" required></td>
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

        // Inisialisasi input progres
        var jumlahProgres = $('#jumlah_progres').val();
        if (jumlahProgres > 0) {
            generateProgressFields(jumlahProgres);
        }

        // Hitung total nilai jam kompen saat halaman dimuat jika ada progres
        $('input[name="jam_kompen[]"]').each(function() {
            calculateTotalNilai();
        });

        // Handle input tag (persyaratan)
        const tagInput = document.getElementById("tag-input");
        const tagContainer = document.getElementById("tag-container");

        tagInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter" && tagInput.value.trim() !== "") {
                event.preventDefault();
                addTag(tagInput.value.trim());
                tagInput.value = "";
            }
        });

        // Menambah tag persyaratan
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

        // Memperbarui input hidden persyaratan
        function updatePersyaratanInput() {
            let tags = [];
            document.querySelectorAll('#tag-container .tag').forEach(tag => {
                tags.push(tag.innerText.replace("×", "").trim());
            });
            document.getElementById('persyaratan-hidden').value = JSON.stringify(
                tags); // Menyimpan dalam format JSON
        }

        // Form validation dan pengiriman data
        $("#form-tambah").validate({
            submitHandler: function(form) {
                const formData = $(form).serializeArray();
                // Tangkap nilai dari select multiple
                const kompetensiValues = $('#kompetensi_id').val();
                formData.push({
                    name: 'kompetensi_id',
                    value: kompetensiValues
                });
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
    });
</script>

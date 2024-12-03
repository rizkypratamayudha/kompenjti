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
                    <label for="kompetensi_id">Kompetensi</label>
                    <select name="kompetensi_id[]" id="kompetensi_id" class="form-control select2" multiple="multiple" required>
                        @foreach ($kompetensi as $item)
                            <option value="{{ $item->kompetensi_admin_id }}"{{ in_array($item->kompetensi_admin_id, $pekerjaan->kompetensi_ids ?? []) ? ' selected' : '' }}>
                                {{ $item->kompetensi_nama }}
                            </option>
                        @endforeach
                    </select>
                    <small id="error-kompetensi_id" class="error-text form-text text-danger"></small>
                </div>
                <div id="selected-tags" class="mt-3">
                    <!-- Bubble tags from database will be displayed here -->
                    @if(!empty($pekerjaan->kompetensi_ids))
                        @foreach ($pekerjaan->kompetensi_ids as $kompetensiId)
                            @php
                                $kompetensiNama = $kompetensi->firstWhere('kompetensi_admin_id', $kompetensiId)->kompetensi_nama ?? 'Unknown';
                            @endphp
                            <span class="bubble-tag" data-id="{{ $kompetensiId }}">
                                {{ $kompetensiNama }}<span class="remove-tag" data-value="{{ $kompetensiId }}">×</span>
                            </span>
                        @endforeach
                    @endif
                </div>
                
                
                <!-- Deskripsi Tugas -->
                <div class="form-group">
                    <label>Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi_tugas" id="deskripsi_tugas" cols="30" rows="5" class="form-control">{{ $pekerjaan->detail_pekerjaan->deskripsi_tugas ?? '' }}</textarea>
                </div>

                <div class="form-group">
                    <div class="form-group">
                        <label>Jumlah Progres</label>
                        <input value="{{ $pekerjaan->progres->count() }}" type="number" name="jumlah_progres"
                            id="jumlah_progres" class="form-control" required readonly>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <button type="button" id="add-row" class="btn btn-info btn-sm">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <table class="table table-bordered" id="table-progres">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Progres</th>
                                <th>Nilai Jam Kompen</th>
                                <th>Hari</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="progres-rows">
                            @foreach($pekerjaan->progres as $index => $progres)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <input type="text" name="judul_progres[]" value="{{ $progres->judul_progres }}" class="form-control" placeholder="Judul Progres" required>
                                </td>
                                <td>
                                    <input type="number" name="jam_kompen[]" value="{{ $progres->jam_kompen }}" class="form-control nilai-jam-kompen" placeholder="Nilai Jam Kompen" required>
                                </td>
                                <td>
                                    <input type="text" name="hari[]" value="{{ $progres->hari }}" class="form-control" placeholder="Hari" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                </td>
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
                <button type="button" class="btn btn-danger delete-btn" data-id="{{ $pekerjaan->pekerjaan_id }}">Hapus</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<style>
#selected-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.bubble-tag {
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    display: inline-block;
    margin-bottom: 5px;
}
.bubble-tag .remove-tag {
    cursor: pointer;
    margin-left: 8px;
    font-weight: bold;
    color: white;
}


.tag-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    align-items: center;
    padding: 5px;
    min-height: 40px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.tag {
    display: flex;
    align-items: center;
    background-color: #007bff;
    color: #fff;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 14px;
}

.tag .remove-tag {
    margin-left: 10px;
    cursor: pointer;
    color: #fff;
    font-weight: bold;
}

.tag-input {
    border: none;
    outline: none;
    flex-grow: 1;
    min-width: 100px;
    font-size: 14px;
    padding: 5px;
}

.tag-input:focus {
    outline: none;
}

</style>
<script>
   $(document).ready(function () {
    // Inisialisasi Select2 tanpa menampilkan tags yang sudah ada
    $('#kompetensi_id').select2({
        placeholder: 'Pilih kompetensi',
        allowClear: true,
    });

    // Update tags bubble dari pilihan baru
    function updateTags() {
        const selectedOptions = $('#kompetensi_id').select2('data');
        const tagsContainer = $('#selected-tags');
        tagsContainer.empty(); // Hapus semua bubble tag yang ada

        selectedOptions.forEach(option => {
            const tag = $(
                `<span class="bubble-tag" data-id="${option.id}">${option.text}<span class="remove-tag" data-value="${option.id}">×</span></span>`
            );
            tagsContainer.append(tag);
        });
    }

    // Hapus tag saat tanda "×" diklik
    $(document).on('click', '.remove-tag', function () {
        const valueToRemove = $(this).data('value');
        const select = $('#kompetensi_id');
        const selectedValues = select.val() || [];
        const index = selectedValues.indexOf(valueToRemove.toString());

        if (index !== -1) {
            selectedValues.splice(index, 1); // Hapus dari array
            select.val(selectedValues).trigger('change'); // Update Select2
        }
        $(this).parent().remove(); // Hapus tag bubble
    });

    // Update bubble saat pilihan berubah
    $('#kompetensi_id').on('change', updateTags);

    // Inisialisasi pertama (untuk data dari database)
    updateTags();

    // Handle input tag (persyaratan)
    const tagInput = document.getElementById("tag-input");
    const tagContainer = document.getElementById("tag-container");

    tagInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter" && tagInput.value.trim() !== "") {
            event.preventDefault();
            addTag(tagInput.value.trim());
            tagInput.value = "";
        }
    });

    // Add new tag
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

    // Update hidden input for persyaratan
    function updatePersyaratanInput() {
        let tags = [];
        document.querySelectorAll('#tag-container .tag').forEach(tag => {
            tags.push(tag.innerText.replace("×", "").trim());
        });
        document.getElementById('persyaratan-hidden').value = JSON.stringify(tags); // Save as JSON format
    }

    // Remove tag
    window.removeTag = function (element) {
        const tag = element.parentElement;
        tagContainer.removeChild(tag);
        updatePersyaratanInput();
    };

        // Handle perubahan status pekerjaan
        $('#status_pekerjaan').on('change', function() {
            var status = $(this).prop('checked') ? 'open' : 'close';
            $('#status-label').text(status.charAt(0).toUpperCase() + status.slice(1));
            $('#status-input').val(status);
        });

        const $progresRows = $("#progres-rows");
        const $jumlahProgres = $("#jumlah_progres");
        const $totalNilai = $("#total-nilai");

        // Fungsi untuk menambahkan baris
        $("#add-row").on("click", function () {
            const index = $progresRows.children("tr").length + 1; // Hitung jumlah baris
            const newRow = `
                <tr>
                    <td>${index}</td>
                    <td><input type="text" name="judul_progres[]" class="form-control" placeholder="Judul Progres" required></td>
                    <td><input type="number" name="jam_kompen[]" class="form-control nilai-jam-kompen" placeholder="Nilai Jam Kompen" required></td>
                    <td><input type="text" name="hari[]" class="form-control" placeholder="Hari" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            $progresRows.append(newRow);
            updateJumlahProgres(); // Perbarui jumlah progres
            attachInputEvent(); // Pasang event input
        });

        // Fungsi untuk menghapus baris
        $progresRows.on("click", ".remove-row", function () {
            $(this).closest("tr").remove(); // Hapus baris
            updateJumlahProgres(); // Perbarui jumlah progres
            updateRowNumbers(); // Perbarui nomor urut
            calculateTotalNilai(); // Hitung ulang total nilai
        });

        // Fungsi untuk memperbarui jumlah progres
        function updateJumlahProgres() {
            const jumlahBaris = $progresRows.children("tr").length; // Hitung jumlah baris
            $jumlahProgres.val(jumlahBaris); // Set nilai jumlah progres
        }

        // Fungsi untuk menghitung total nilai jam kompen
        function calculateTotalNilai() {
            let totalNilai = 0;
            $(".nilai-jam-kompen").each(function () {
                totalNilai += parseFloat($(this).val()) || 0; // Tambahkan nilai atau 0 jika kosong
            });
            $totalNilai.text(totalNilai); // Tampilkan total nilai
        }

        // Fungsi untuk memperbarui nomor urut setelah penghapusan
        function updateRowNumbers() {
            $progresRows.children("tr").each(function (index) {
                $(this).find("td:first").text(index + 1); // Atur ulang nomor urut
            });
        }

        // Fungsi untuk menambahkan event input pada jam kompen
        function attachInputEvent() {
            $(".nilai-jam-kompen").off("input").on("input", function () {
                calculateTotalNilai(); // Hitung ulang total nilai saat input berubah
            });
        }

        // Inisialisasi saat halaman dimuat
        $(document).ready(function () {
            updateJumlahProgres(); // Perbarui jumlah progres
            attachInputEvent(); // Pasang event input
            calculateTotalNilai(); // Hitung total nilai awal
        });



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

        // Fungsi hapus pekerjaan
        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();

            // Ambil ID dari atribut data-id
            let pekerjaanId = $(this).data('id');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Tampilkan konfirmasi menggunakan SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lakukan penghapusan via Ajax
                    $.ajax({
                        url: `dosen/${pekerjaanId}/delete_ajax`, // URL sesuai dengan route
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload(); // Refresh halaman jika berhasil
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Error'),
                                'error'
                            );
                        }
                    });
                }
            });
        });

    });
</script>

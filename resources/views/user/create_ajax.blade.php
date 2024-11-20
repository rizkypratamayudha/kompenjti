<form action="{{ url('/user/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Role Pengguna</label>
                    <select name="level_id" id="level_id" class="form-control" required>
                        <option value="">- Pilih Role -</option>
                        @foreach($level as $item)
                            <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="error-text form-text text-danger"></small>
                </div>

                <!-- General Fields -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                    <small id="error-nama" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                    <small id="error-username" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>

                <!-- Additional Fields -->
                <div id="additional-fields">
                    <div id="email-field" class="form-group" style="display: none;">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                        <small id="error-email" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="no-hp-field" class="form-group" style="display: none;">
                        <label>No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control">
                        <small id="error-no_hp" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="prodi-field" class="form-group" style="display: none;">
                        <label>Prodi</label>
                        <select name="prodi_id" id="prodi_id" class="form-control">
                            <option value="">- Pilih Prodi -</option>
                            @foreach($prodi as $item)
                                <option value="{{ $item->prodi_id }}">{{ $item->prodi_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-prodi_id" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="angkatan-field" class="form-group" style="display: none;">
                        <label>Angkatan</label>
                        <input type="text" name="angkatan" id="angkatan" class="form-control">
                        <small id="error-angkatan" class="error-text form-text text-danger"></small>
                    </div>

                    <div id="periode-field" class="form-group" style="display: none;">
                        <label>Periode</label>
                        <input type="text" name="periode_id" id="periode_id" class="form-control">
                        <small id="error-periode_id" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

   

<script>
    $(document).ready(function () {
        // Trigger form field visibility based on Role
        $('#level_id').on('change', function () {
            const role = $(this).val();
            resetAdditionalFields();
            if (role === '3') { // Mahasiswa
                toggleFields(['email', 'no_hp', 'prodi_id', 'angkatan', 'periode_id'], true);
            } else if (role === '4') { // Kaprodi
                toggleFields(['email', 'no_hp', 'prodi_id'], true);
            } else if (role === '1' || role === '2') { // Admin, Dosen
                toggleFields(['email', 'no_hp'], true);
            }
        }).trigger('change'); // Trigger saat halaman dimuat

        function toggleFields(fields, show) {
            fields.forEach(field => {
                const fieldDiv = $(`#${field}-field`);
                const fieldInput = $(`#${field}`);
                if (show) {
                    fieldDiv.show();
                    fieldInput.prop('required', true);
                } else {
                    fieldDiv.hide();
                    fieldInput.prop('required', false);
                    fieldInput.val('');
                }
            });
        }

        function resetAdditionalFields() {
            toggleFields(['email', 'no_hp', 'prodi_id', 'angkatan', 'periode_id'], false);
        }

        // Reset Form on Modal Close
        $('#modal-master').on('hidden.bs.modal', function () {
            $('#form-tambah')[0].reset();
            resetAdditionalFields();
        });
    });
</script>

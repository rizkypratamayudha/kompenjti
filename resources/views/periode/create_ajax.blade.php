<form action="{{ url('/periode/ajax') }}" method="POST" id="form-tambah">
     @csrf
     <div id="modal-master" class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Tambah Data Periode</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <div class="form-group">
                     <label>Periode ID</label>
                     <input type="text" name="periode_id" id="periode_id" class="form-control" required>
                     <small id="error-periode_id" class="error-text form-text text-danger"></small>
                 </div>
                 <div class="form-group">
                     <label>Nama Periode</label>
                     <input type="text" name="periode_nama" id="periode_nama" class="form-control" required>
                     <small id="error-periode_nama" class="error-text form-text text-danger"></small>
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
     $(document).ready(function() {
         $("#form-tambah").validate({
             rules: {
                 periode_id: { required: true, minlength: 3, maxlength: 20 },
                 periode_nama: { required: true, minlength: 3, maxlength: 100 }
             },
             submitHandler: function(form) {
                 $.ajax({
                     url: form.action,
                     type: form.method,
                     data: $(form).serialize(),
                     success: function(response) {
                         if (response.status) {
                             $('#modal-master').modal('hide');
                             $('#myModal').modal('hide');
                             Swal.fire({
                                 icon: 'success',
                                 title: 'Berhasil',
                                 text: response.message
                             });
                             // Reload DataTable
                             dataPeriode.ajax.reload();
                         } else {
                             $('.error-text').text(''); // Reset error messages
                             $.each(response.msgField, function(prefix, val) {
                                 $('#error-' + prefix).text(val[0]); // Show error
                             });
                             Swal.fire({
                                 icon: 'error',
                                 title: 'Terjadi Kesalahan',
                                 text: response.message
                             });
                         }
                     },
                     error: function(xhr) {
                         Swal.fire({
                             icon: 'error',
                             title: 'Error',
                             text: 'Terjadi kesalahan pada server.'
                         });
                     }
                 });
                 return false; // Prevent default form submission
             },
             errorElement: 'span',
             errorPlacement: function(error, element) {
                 error.addClass('invalid-feedback');
                 element.closest('.form-group').append(error);
             },
             highlight: function(element) {
                 $(element).addClass('is-invalid');
             },
             unhighlight: function(element) {
                 $(element).removeClass('is-invalid');
             }
         });
     });
 </script>

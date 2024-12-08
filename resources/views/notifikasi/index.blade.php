@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: '{{ session('success') }}',
                        showConfirmButton: true
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: '{{ session('error') }}',
                        showConfirmButton: true
                    });
                </script>
            @endif

            @foreach ($notifikasi as $item)
                <div class="card-body mb-0" id="notifikasi-{{ $item->notifikasi_id }}">
                    <blockquote class="blockquote mb-0 mt-0">
                        <div class="row">
                            <!-- Icon -->
                            <div class="col-auto d-flex align-items-center ml-3 mr-1">
                                <i class="fa-solid fa-bell fa-2x
                                    {{ str_starts_with($item->pesan, 'Coba') || str_starts_with($item->pesan, 'Mohon') ? 'text-danger' : (str_starts_with($item->pesan, 'Selamat') || str_starts_with($item->pesan, 'Jam') ? 'text-success' : '') }}">
                                </i>
                            </div>
                            <!-- Text Content -->
                            <div class="col">
                                <p class="{{ str_starts_with($item->pesan, 'Coba') || str_starts_with($item->pesan, 'Mohon') ? 'text-danger' : (str_starts_with($item->pesan, 'Selamat') || str_starts_with($item->pesan, 'Jam') ? 'text-success' : '') }}">
                                    {{ $item->pesan }} ({{ $item->pekerjaan->pekerjaan_nama ?? '' }})
                                </p>
                                <footer class="blockquote-footer">
                                    @if (str_starts_with($item->pesan, 'Selamat!!, Request'))
                                    {{ $item->kaprodi->nama }} -
                                    {{ $item->created_at->locale('in_ID')->diffForHumans() }}
                                    @else
                                    {{ $item->pekerjaan->user->nama }} -
                                    {{ $item->created_at->locale('in_ID')->diffForHumans() }}
                                    @endif
                                </footer>
                                <div class="d-flex justify-content-end mt-2">
                                    <button class="btn btn-outline-success btn-sm" onclick="tandaiDibaca({{ $item->notifikasi_id }})">Tandai Dibaca</button>
                                </div>
                            </div>
                        </div>
                    </blockquote>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('js')
<script>
    function tandaiDibaca(id) {
        // Lakukan request AJAX untuk menghapus notifikasi
        $.ajax({
            url: '{{ route("notifikasi.dibaca", ":id") }}'.replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    // Tampilkan notifikasi sukses menggunakan SweetAlert
                    Swal.fire('Sukses', response.message, 'success').then(() => {
                        // Hapus elemen notifikasi dari tampilan
                        $('#notifikasi-' + id).remove();
                    });
                } else {
                    // Tampilkan notifikasi error menggunakan SweetAlert
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function() {
                // Menampilkan error jika ada kesalahan pada permintaan AJAX
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus notifikasi', 'error');
            }
        });
    }
</script>
@endpush

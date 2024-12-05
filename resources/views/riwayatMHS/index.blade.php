@extends('layouts.template')


@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="level_id" name="level_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($tugas as $item)
                                    <option value="{{ $item->jenis_pekerjaan }}">{{ $item->jenis_pekerjaan }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Jenis Pekerjaan</small>
                        </div>
                    </div>
                </div>
            </div>

            @if ($tugas->isEmpty())
                <div class="text-center">
                    <img src="{{ asset('pekerjaan_kosong.png') }}" alt="No Tasks"
                        style="max-width: 200px; margin-bottom: 15px;">
                    <p class="mt-3">Silahkan Melamar Pekerjaan</p>
                </div>
            @endif

            <div class="row">
                @foreach ($tugas as $item)
                    <div class="col-sm-6 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $item->pekerjaan_nama }}</h5>
                                @if ($item->canRequestSurat)
                                    <div class="d-flex justify-content-center ml-4">
                                        <form method="POST" action="{{url('riwayat/' .$item->pekerjaan_id. '/requestcetaksurat')}}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-file-contract"></i> Request Cetak Surat
                                            </button>
                                        </form>
                                    </div>
                                @endif
                                <button onclick="modalAction('{{ 'riwayat/' . $item->pekerjaan_id . '/show_ajax' }}')"
                                    class="ml-auto btn btn-blue">
                                    <i class="fa-solid fa-info-circle" style="color: #ffffff"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <small class="text-muted">Deskripsi Tugas :</small><br>
                                    {{ $item->detail_pekerjaan->deskripsi_tugas }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Jumlah Anggota:
                                        <span id="jumlah-anggota-{{ $item->pekerjaan_id }}">0</span> /
                                        {{ $item->detail_pekerjaan->jumlah_anggota }}
                                    </small>
                                    <a href="{{ url('riwayat/' . $item->pekerjaan_id . '/riwayatmhs') }}"
                                        class="btn btn-outline-primary btn-sm">Masuk</a>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Jumlah Nilai Jam Kompen:
                                        {{ $item->jumlah_jam_kompen }}</small>
                                </div>
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                <i class="fas fa-clock text-muted"></i>
                                Terakhir diperbarui: {{ $item->updated_at->locale('in_id')->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }


        // Fungsi untuk memuat jumlah anggota
        function loadAnggota(pekerjaanId) {
            $.ajax({
                url: '{{ url('/pekerjaan') }}/' + pekerjaanId + '/get-anggota',
                method: 'GET',
                success: function(response) {
                    if (response.status) {
                        // Update jumlah anggota di elemen HTML
                        $('#jumlah-anggota-' + pekerjaanId).text(response.anggotaJumlah);
                    } else {
                        console.error('Gagal memuat jumlah anggota untuk pekerjaan ID: ' + pekerjaanId);
                    }
                },
                error: function() {
                    console.error('Error loading anggota count for pekerjaan ID: ' + pekerjaanId);
                }
            });
        }

        // Periksa status Apply dan jumlah anggota untuk setiap pekerjaan saat halaman dimuat
        $(document).ready(function() {
            @foreach ($tugas as $item)
                loadAnggota({{ $item->pekerjaan_id }}); // Memuat jumlah anggota untuk setiap pekerjaan
            @endforeach
        });
    </script>
@endpush

@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button class="btn btn-sm mt-1 btn-primary" onclick="modalAction('{{ 'dosen/create_ajax' }}')"><i
                        class="fas fa-plus-circle"></i> Pekerjaan</button>
            </div>
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
                    <p class="mt-3">Belum membuat pekerjaan!!!</p>
                </div>
            @endif
            <div class="row">
                @foreach ($tugas as $item)
                    <div class="col-sm-6 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $item->pekerjaan_nama }}</h5>
                                <button class="ml-auto btn btn-m btn-outline-blue"
                                    onclick="modalAction('{{ 'dosen/' . $item->pekerjaan_id . '/edit_ajax' }}')">
                                    <i class="fa-solid fa-gear" style="color: #ffffff;"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <small class="text-muted">Status Pekerjaan :</small><br>
                                    {{ $item->status }}
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">Deskripsi Tugas :</small><br>
                                    {{ $item->detail_pekerjaan->deskripsi_tugas }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text" style="color: #ff0000">
                                        Jumlah Pelamar:
                                        <span id="notif-pelamar-{{ $item->pekerjaan_id }}" style="color: #ff0000">0</span>
                                    </small>
                                    <small class="text-muted">Jumlah Anggota:
                                        <span id="jumlah-anggota-{{ $item->pekerjaan_id }}">0</span> /
                                        {{ $item->detail_pekerjaan->jumlah_anggota }}
                                    </small>
                                    <a href="{{ url('dosen/' . $item->pekerjaan_id . '/pekerjaan') }}"
                                        class="btn btn-outline-primary btn-sm">Masuk</a>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Jumlah Nilai Jam Kompen:
                                        {{ $item->jumlah_jam_kompen }}</small>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <i class="fas fa-clock text-muted"></i>
                                Terakhir diperbarui: {{ $item->updated_at->locale('in_id')->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
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

            function loadPelamarCount(pekerjaanId) {
                $.ajax({
                    url: '{{ url('/dosen') }}/' + pekerjaanId + '/hitung-notif',
                    method: 'GET',
                    success: function(response) {
                        if (response.jumlah !== undefined) {
                            $('#notif-pelamar-' + pekerjaanId).text(response.jumlah);
                        } else {
                            console.error('Gagal mendapatkan jumlah pelamar untuk ID: ' + pekerjaanId);
                        }
                    },
                    error: function() {
                        console.error('Error loading pelamar count for ID: ' + pekerjaanId);
                    }
                });
            }

            // Memuat data jumlah pelamar untuk setiap pekerjaan
            $(document).ready(function() {
                @foreach ($tugas as $item)
                    loadPelamarCount({{ $item->pekerjaan_id }});
                @endforeach
            });



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

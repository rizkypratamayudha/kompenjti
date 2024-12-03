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

            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Header Section -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="assignment-icon mr-4">
                                <i class="fa-regular fa-file"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="mb-0">Progres : {{ $progres->judul_progres }}</h4>
                                <div class="text-secondary">
                                    <small>{{ $progres->pekerjaan->user->nama }} •
                                        {{ $progres->pekerjaan->created_at->locale('in_ID')->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="ms-auto px-4">
                                <p class="mb-0">Deadline : @if ($progres->deadline)
                                        {{ \Carbon\Carbon::parse($progres->deadline)->format('d M Y, H:i') }}
                                    @endif
                                </p>
                            </div>

                        </div>

                        <!-- Comments Section -->
                        <div class="mt-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-hourglass mr-2" style="color: #1a73e8"></i>
                                <span class="text-secondary">Nilai Progres : {{ $progres->jam_kompen }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="col-md-4">
                        <!-- Dropdown Section -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Pekerjaan</h6>
                                    @if ($progres->pengumpulan_id == null)
                                        <span class="text-secondary">
                                            Belum diserahkan
                                        </span>
                                    @else
                                        <span class="text-secondary">
                                            Nilai :
                                            @if ($pengumpulan->status == 'pending')
                                                -
                                            @elseif ($pengumpulan->status == 'accept')
                                                {{ $progres->jam_kompen }}
                                            @elseif ($pengumpulan->status == 'decline')
                                                0
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="text-center">
                                    @if ($progres->pengumpulan_id == null)
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            {{-- @php
                                                $isExpired =
                                                    $progres->deadline &&
                                                    \Carbon\Carbon::now()->greaterThan(
                                                        \Carbon\Carbon::parse($progres->deadline),
                                                    );
                                            @endphp --}}

                                            <button class="btn btn-primary dropdown-toggle w-100" type="button"
                                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{-- {{ $isExpired ? 'disabled' : '' }}> --}}
                                                <i class="fa-solid fa-plus me-2"></i> Tambah atau Buat
                                            </button>


                                            <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-4"
                                                        onclick="modalAction('{{ url('/riwayat/' . $progres->progres_id . '/link_ajax') }}')"
                                                        href="#">
                                                        <i class="fa-solid fa-link"></i>
                                                        Link
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-4" href="#"
                                                        onclick="modalAction('{{ url('/riwayat/' . $progres->progres_id . '/gambar_ajax') }}')">
                                                        <i class="fa-regular fa-image"></i>
                                                        Gambar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-4" href="#"
                                                        onclick="modalAction('{{ url('/riwayat/' . $progres->progres_id . '/file_ajax') }}')">
                                                        <i class="fa-solid fa-paperclip"></i>
                                                        File
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                </div>
                                @endif

                                @if ($progres->pengumpulan_id != null)
                                    <div class="mt-5 mb-2">
                                        <button class="btn btn-danger w-100"
                                            onclick="modalAction('{{ url('/riwayat/' . $progres->progres_id . '/hapus_ajax') }}')">Batalkan
                                            pengiriman</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <span>Pekerjaan yang dikumpulkan : </span>
                            </div>

                            @if ($pengumpulan && $pengumpulan->bukti_pengumpulan)
                                @if (str_starts_with($pengumpulan->bukti_pengumpulan, 'https://'))
                                    <!-- Jika bukti_pengumpulan adalah URL yang dimulai dengan 'https://' -->
                                    <a class="text-decoration-none" href="{{ $pengumpulan->bukti_pengumpulan }}">
                                        {{ $pengumpulan->bukti_pengumpulan }}
                                    </a>
                                @elseif(str_starts_with($pengumpulan->bukti_pengumpulan, 'pengumpulan_gambar/'))
                                    <!-- Jika bukti_pengumpulan adalah file yang disimpan di folder 'pengumpulan_gambar/' -->
                                    <img class="text-decoration-none mt-3"
                                        src="{{ asset('storage/' . $pengumpulan->bukti_pengumpulan) }}"
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; ">
                                @else
                                    <!-- Jika bukti_pengumpulan tidak memenuhi kedua kondisi di atas, tampilkan '-'' -->
                                    <span>-</span>
                                @endif
                            @else
                                <!-- Jika $pengumpulan atau bukti_pengumpulan tidak ditemukan -->
                                <span>-</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
                data-keyboard="false" data-width="75%" aria-hidden="true"></div>

            <!-- Styles -->
            <style>
                .assignment-icon {
                    width: 40px;
                    height: 40px;
                    background-color: #1a73e8;
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }

                .dropdown-menu {
                    text-align: center;
                }

                .dropdown-item {
                    padding: 10px 15px;
                    transition: background-color 0.3s ease;
                }

                .dropdown-item:hover {
                    background-color: #f8f9fa;
                }

                .hover-effect:hover {
                    background-color: #f8f9fa;
                    cursor: pointer;
                }

                .dropdown-item i {
                    margin-right: 15px;
                    /* Atur jarak ikon dengan teks */
                }
            </style>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        const serverTimeUrl = "{{ route('server-time') }}";

        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('dropdownMenuButton');
            const deadline = new Date("{{ $progres->deadline }}"); // Ambil deadline dari backend

            fetch(serverTimeUrl)
                .then(response => response.json())
                .then(data => {
                    const serverTime = new Date(data.server_time); // Waktu server dari API
                    if (serverTime > deadline) {
                        button.disabled = true; // Nonaktifkan tombol jika waktu server melebihi deadline
                    }
                })
                .catch(error => {
                    console.error('Error fetching server time:', error);
                });
        });
    </script>
@endpush

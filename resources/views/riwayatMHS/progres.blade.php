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
                                    <span class="text-secondary">Belum diserahkan</span>
                                </div>
                                <div class="text-center">
                                    <!-- Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle w-100" type="button"
                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                    onclick="modalAction('{{ url('/riwayat/'. $progres->progres_id . '/gambar_ajax') }}')">
                                                    <i class="fa-regular fa-image"></i>
                                                    Gambar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-4" href="#" onclick="modalAction('{{url('/riwayat/' . $progres->progres_id . '/file_ajax')}}')">
                                                    <i class="fa-solid fa-paperclip"></i>
                                                    File
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-link text-danger w-100">Batalkan pengiriman</button>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-circle"></i>
                                    <span>Komentar pribadi</span>
                                </div>
                                <a href="#" class="text-decoration-none">Tambahkan komentar untuk
                                    {{ $progres->pekerjaan->user->nama }}</a>
                            </div>
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
    </script>
@endpush

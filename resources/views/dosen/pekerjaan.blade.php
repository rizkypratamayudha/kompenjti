@extends('layouts.template')

@section('content')
    <div class="card-header">
        <div class="banner">
            <h1>{{ $pekerjaan->pekerjaan_nama }}</h1>
            <div class="image-container">
                <!-- Add icons here, using placeholder for now -->
            </div>
        </div>
        <style>
            /* Basic Reset */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* Main container styling */
            .banner {
                background-color: #2596be;
                /* Teal color as shown in the image */
                color: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px;
                border-radius: 8px;
            }

            /* Text styling */
            .banner h1 {
                font-size: 24px;
                font-weight: bold;
                align-items: flex-end;
            }

            /* Image section */
            .banner .image-container {
                display: flex;
                align-items: center;
            }

            .banner .image-container img {
                max-width: 100px;
                /* Adjust size according to design */
                margin-left: 10px;
            }
        </style>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card text-center">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'progres' ? 'active' : '' }}" id="progresTab" href="#"
                            onclick="loadProgres({{ $pekerjaan->pekerjaan_id }})">Progress</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'anggota' ? 'active' : '' }}" id="anggotaTab" href="#"
                            onclick="loadAnggota({{ $pekerjaan->pekerjaan_id }})">Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'pelamaran' ? 'active' : '' }}" id="pelamaranTab" href="#"
                            onclick="loadPelamaran({{ $pekerjaan->pekerjaan_id }})">Pelamaran</a>
                    </li>
                </ul>

            </div>
            <div class="card-body" id="tabContent">
                <div id="progresContent" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="pelamaranContent" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="anggotaContent" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.9/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.9/locale/id.js"></script>

    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        function loadProgres(pekerjaanId) {
            document.getElementById('pelamaranContent').style.display = 'none';
            document.getElementById('anggotaContent').style.display = 'none';

            // Tampilkan konten progres
            const progresContent = document.getElementById('progresContent');
            progresContent.style.display = 'block';
            progresContent.innerHTML = `
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;

            fetch(`{{ url('dosen') }}/${pekerjaanId}/get-progres`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        let progresHtml = '';
                        data.data.forEach((progres, index) => {
                            progresHtml += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">${progres.judul_progres}</h5>
                                <p class="card-text">Nilai jam per progres: ${progres.jam_kompen}</p>
                                <p class="card-text"><small class="text-muted">Deadline: ${progres.hari} Hari</small></p>
                                <button class="btn btn-sm btn-primary">Info</button>
                            </div>
                        </div>
                    `;
                        });
                        progresContent.innerHTML = progresHtml;
                    } else {
                        progresContent.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching progres:', error);
                    progresContent.innerHTML = `<div class="alert alert-danger">Gagal memuat progres.</div>`;
                });

            setActiveTab('progres');
        }

        function loadPelamaran(pekerjaanId) {
            // Sembunyikan semua konten tab lainnya
            document.getElementById('progresContent').style.display = 'none';
            document.getElementById('anggotaContent').style.display = 'none';

            // Tampilkan konten pelamaran
            const pelamaranContent = document.getElementById('pelamaranContent');
            pelamaranContent.style.display = 'block';
            pelamaranContent.innerHTML = `
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;

            dayjs.extend(dayjs_plugin_relativeTime);
            dayjs.locale('id');

            fetch(`{{ url('dosen') }}/${pekerjaanId}/get-pelamaran`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        let pelamaranHtml = '';
                        data.data.forEach((pelamar, index) => {
                            pelamaranHtml += `
                    <div class="card mb-3">
                        <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <h5 class="card-title">Nama : ${pelamar.user.nama}</h5><br>
                                <h5 class="card-title">NIM : ${pelamar.user.username}</h5><br>
                                <h5 class="card-title">Prodi : ${pelamar.user.detail_mahasiswa.prodi.prodi_nama}</h5><br>
                                <h5 class="card-title">Email : ${pelamar.user.detail_mahasiswa.email}</h5><br>
                                <h5 class="card-title">No HP : ${pelamar.user.detail_mahasiswa.no_hp}</h5><br>

                            </div>
                            <div class="col-3 ml-auto">
                                <p class="card-text"><small class="text-muted">Melamar ${dayjs(pelamar.created_at).fromNow()}</small></p>
                                <button class="btn btn-outline-info btn-lg" onclick="modalAction('{{ url('/dosen/') }}/${pelamar.user.user_id}/lihat-pekerjaan')"
>Lihat</button>
                                <button onclick="approvePekerjaan(${pelamar.user.user_id}, {{ $pekerjaan->pekerjaan_id }})" class="btn btn-outline-success btn-lg"><i class="fas fa-check-circle"></i></button>
                                <button onclick="declinePekerjaan(${pelamar.user.user_id}, {{ $pekerjaan->pekerjaan_id }})" class="btn btn-outline-danger btn-lg"><i class="fas fa-circle-xmark"></i></button>
                            </div>
                        </div>
                        </div>
                    </div>
                `;
                        });
                        pelamaranContent.innerHTML = pelamaranHtml;
                    } else {
                        pelamaranContent.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching pelamaran:', error);
                    pelamaranContent.innerHTML = `<div class="alert alert-danger">Gagal memuat pelamaran.</div>`;
                });

            setActiveTab('pelamaran');
        }

        function loadAnggota(pekerjaanId) {
            // Sembunyikan semua konten tab lainnya
            document.getElementById('progresContent').style.display = 'none';
            document.getElementById('pelamaranContent').style.display = 'none';

            // Tampilkan konten anggota
            const anggotaContent = document.getElementById('anggotaContent');
            anggotaContent.style.display = 'block';
            anggotaContent.innerHTML = `
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;

            dayjs.extend(dayjs_plugin_relativeTime);
            dayjs.locale('id');

            // Fetch data dari server
            fetch(`{{ url('dosen') }}/${pekerjaanId}/get-anggota`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        let anggotaHtml = '';
                        data.data.forEach((anggota, index) => {
                            anggotaHtml += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <h5 class="card-title">Nama: ${anggota.user.nama}</h5><br>
                                        <h5 class="card-title">NIM: ${anggota.user.username}</h5><br>
                                        <h5 class="card-title">Prodi: ${anggota.user.detail_mahasiswa.prodi.prodi_nama}</h5><br>
                                        <h5 class="card-title">Email: ${anggota.user.detail_mahasiswa.email}</h5><br>
                                        <h5 class="card-title">No HP: ${anggota.user.detail_mahasiswa.no_hp}</h5><br>
                                    </div>
                                    <div class="col-3 ml-auto">
                                        <p class="card-text"><small class="text-muted">Disetujui ${dayjs(anggota.created_at).fromNow()}</small></p>
                                        <button class="btn btn-outline-info btn-lg" onclick="modalAction('{{ url('/dosen/') }}/${anggota.user.user_id}/lihat-pekerjaan')">Lihat</button>
                                        <button onclick="kickPekerjaan(${anggota.user.user_id}, {{ $pekerjaan->pekerjaan_id }})" class="btn btn-outline-danger btn-lg"><i class="fas fa-circle-xmark"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        });
                        anggotaContent.innerHTML = anggotaHtml;
                    } else {
                        anggotaContent.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching anggota:', error);
                    anggotaContent.innerHTML = `<div class="alert alert-danger">Gagal memuat anggota.</div>`;
                });

            setActiveTab('anggota');
        }



        function approvePekerjaan(userId, pekerjaanId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menyetujui pelamar ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('dosen') }}/approve-pekerjaan`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                user_id: userId,
                                pekerjaan_id: pekerjaanId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire(
                                    'Berhasil!',
                                    data.message,
                                    'success'
                                );
                                loadPelamaran(pekerjaanId);
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error approving pekerjaan:', error);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menyetujui pekerjaan.',
                                'error'
                            );
                        });
                }
            });
        }


        function declinePekerjaan(userId, pekerjaanId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Pelamar ini akan ditolak!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan ke server
                    fetch(`{{ url('dosen') }}/decline-pekerjaan`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Tambahkan token CSRF
                            },
                            body: JSON.stringify({
                                user_id: userId,
                                pekerjaan_id: pekerjaanId,
                                reason: 'Pelamar kurang memenuhi syarat dari pekerjaan',
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire(
                                    'Ditolak!',
                                    data.message,
                                    'success'
                                );
                                // Refresh data pelamaran
                                loadPelamaran(pekerjaanId);
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error declining pekerjaan:', error);
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menolak pekerjaan.',
                                'error'
                            );
                        });
                }
            });
        }

        function kickPekerjaan(userId, pekerjaanId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anggota ini akan dikeluarkan dari pekerjaan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluarkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan ke server
                    fetch(`{{ url('dosen') }}/kick-pekerjaan`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Tambahkan token CSRF
                            },
                            body: JSON.stringify({
                                user_id: userId,
                                pekerjaan_id: pekerjaanId,
                                reason: 'Anggota tidak memenuhi tanggung jawab atau alasan lain.',
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire(
                                    'Dikeluarkan!',
                                    data.message,
                                    'success'
                                );
                                // Refresh data anggota
                                loadAnggota(pekerjaanId);
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error kicking pekerjaan:', error);
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat mengeluarkan anggota.',
                                'error'
                            );
                        });
                }
            });
        }

        function setActiveTab(tab) {
            // Reset semua tab dari class active
            document.getElementById('progresTab').classList.remove('active');
            document.getElementById('anggotaTab').classList.remove('active');
            document.getElementById('pelamaranTab').classList.remove('active');

            // Menambahkan class active ke tab yang dipilih
            if (tab === 'progres') {
                document.getElementById('progresTab').classList.add('active');
            } else if (tab === 'pelamaran') {
                document.getElementById('pelamaranTab').classList.add('active');
            }
        }

        // Call loadProgres function on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProgres({{ $pekerjaan->pekerjaan_id }});
        });
    </script>
@endpush

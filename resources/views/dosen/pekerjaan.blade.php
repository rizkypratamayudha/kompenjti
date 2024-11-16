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
                        <a class="nav-link" id="anggotaTab" href="#">Anggota</a>
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
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function loadProgres(pekerjaanId) {
            document.getElementById('pelamaranContent').style.display = 'none';

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

            // Tampilkan konten pelamaran
            const pelamaranContent = document.getElementById('pelamaranContent');
            pelamaranContent.style.display = 'block';
            pelamaranContent.innerHTML = `
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;

            fetch(`{{ url('dosen') }}/${pekerjaanId}/get-pelamaran`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        let pelamaranHtml = '';
                        data.data.forEach((pelamar, index) => {
                            pelamaranHtml += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Nama : ${pelamar.user.nama}</h5><br>
                            <h5 class="card-title">NIM : ${pelamar.user.username}</h5><br>
                            <h5 class="card-title">Prodi : ${pelamar.user.detail_mahasiswa.prodi.prodi_nama}</h5>
                            <button class="btn btn-sm btn-primary">Detail</button>
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

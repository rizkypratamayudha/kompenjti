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
                        <a class="nav-link active" href="#">Progress</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pelamaran</a>
                    </li>
                </ul>
            </div>
            <div class="card-body" id="tabContent">
                <div id="progresContent" style="display: none;">
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
                                <p class="card-text">Nilai jam per progres : ${progres.jam_kompen}</p>
                                <p class="card-text"><small class="text-muted">Deadline : ${progres.hari} Hari</small></p>
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
        }

        // Call loadProgres function on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProgres({{ $pekerjaan->pekerjaan_id }});
        });
    </script>
@endpush

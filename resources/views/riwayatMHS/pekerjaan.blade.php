@extends('layouts.template')

@section('content')
    <div class="card-header">
        <div class="banner">
            <h1>{{ $pekerjaan->pekerjaan_nama }}</h1>
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
                color: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px;
                border-radius: 8px;
            }

            .banner h1 {
                font-size: 24px;
                font-weight: bold;
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
                        <a href="#" class="nav-link {{ $activeTab == 'progres' ? 'active' : '' }}" id="progresTab"
                            onclick="loadProgres({{ $pekerjaan->pekerjaan_id }})">Progres</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ $activeTab == 'anggota' ? 'active' : '' }}" id="anggotaTab"
                            onclick="loadAnggota({{ $pekerjaan->pekerjaan_id }})">Anggota</a>
                    </li>
                </ul>
            </div>
            <div class="card-body" id="tabContent">
                <div id="progresContent" style="display: none;"></div>
                <div id="anggotaContent" style="display: none;"></div>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
<style>
    .icon-circle {
        width: 40px;
        height: 40px;
        background-color: #007bff; /* Warna biru */
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 20px;
    }
    .task-title {
        font-weight: bold;
        font-size: 1rem;
    }
    .progress-detail {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .ellipsis {
        cursor: pointer;
    }
</style>

@endpush

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
            // Sembunyikan tab lainnya
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
                        data.data.forEach((progres) => {
                            progresHtml += `
                                <div class="d-flex align-items-center border p-3 mb-3 rounded shadow-sm">
                                    <div class="icon-circle me-3">
                                        <i class="fa-regular fa-file"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="task-title">${progres.judul_progres}</div>
                                        <div class="progress-detail">Nilai jam: ${progres.jam_kompen} - Deadline: ${progres.hari} Hari</div>
                                    </div>
                                        <div class="ellipsis">
                                            <button class="btn btn-outline-primary btn-sm" onclick="alert('Info detail untuk ${progres.judul_progres}')">Info</button>
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

        function loadAnggota(pekerjaanId) {
            // Sembunyikan tab lainnya
            document.getElementById('progresContent').style.display = 'none';

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

            fetch(`{{ url('dosen') }}/${pekerjaanId}/get-anggota`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        let anggotaHtml = '';
                        data.data.forEach((anggota) => {
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

        function setActiveTab(tab) {
            document.getElementById('progresTab').classList.remove('active');
            document.getElementById('anggotaTab').classList.remove('active');

            if (tab === 'progres') {
                document.getElementById('progresTab').classList.add('active');
            } else if (tab === 'anggota') {
                document.getElementById('anggotaTab').classList.add('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadProgres({{ $pekerjaan->pekerjaan_id }});
        });
    </script>
@endpush

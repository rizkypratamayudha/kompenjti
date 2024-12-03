@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <!-- Bagian Atas: Informasi Mahasiswa -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center rounded-top">
                        <h4 class="mb-0">Informasi Mahasiswa</h4>
                    </div>
                    <div class="card-body d-flex flex-column flex-md-row align-items-start gap-4">
                        <div class="text-center">
                            <div class="profile-picture mb-3">
                                <img id="profile-avatar"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('user.png') }}"
                                    alt="User Avatar" 
                                    class="img-fluid rounded-circle border border-dark shadow"
                                    style="object-fit: cover; width: 120px; height: 120px;">
                            </div>
                            <h5 class="fw-bold text-primary">{{ $user->nama }}</h5>
                        </div>
                        

                        <!-- Informasi Detail -->
                        <div class="p-4 rounded flex-grow-1 bg-light border shadow-sm">
                            @if ($user && $user->detailMahasiswa)
                                <div class="row mb-2">
                                    <div class="col-12 col-md-6">
                                        <p><i class="bi bi-person-circle text-primary"></i> 
                                            <strong>Username:</strong> {{ $user->username }}
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <p><i class="bi bi-envelope text-primary"></i> 
                                            <strong>Email:</strong> {{ $user->detailMahasiswa->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12 col-md-6">
                                        <p><i class="bi bi-telephone text-primary"></i> 
                                            <strong>No HP:</strong> {{ $user->detailMahasiswa->no_hp }}
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <p><i class="bi bi-calendar3 text-primary"></i> 
                                            <strong>Angkatan:</strong> {{ $user->detailMahasiswa->angkatan }}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <p><i class="bi bi-journal text-primary"></i> 
                                            <strong>Program Studi:</strong> 
                                            {{ $user->detailMahasiswa->prodi->prodi_nama ?? 'Tidak Diketahui' }}
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <p><i class="bi bi-clock text-primary"></i> 
                                            <strong>Periode:</strong> 
                                            {{ $user->detailMahasiswa->periode->periode_nama ?? 'Tidak Diketahui' }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="text-danger">Informasi mahasiswa belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Bawah: Informasi Jam Kompen -->
        {{-- <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-white text-center">
                        <h4>Informasi Jam Kompen</h4>
                    </div>
                    <div class="card-body">
                        @if ($jamkompen)
                            @foreach ($jamkompen as $item)
                                <div class="mb-3">
                                    <p><strong>Periode: </strong>{{ $item->periode->periode_nama }}</p>
                                    <h5>Detail Kompen:</h5>
                                    <ul class="list-group">
                                        @foreach ($item->detail_jamKompen as $detail)
                                            <li class="list-group-item">
                                                <strong>Mata Kuliah:</strong>
                                                {{ $detail->matkul->matkul_nama ?? 'Tidak Diketahui' }} <br>
                                                <strong>Jam:</strong> {{ $detail->jumlah_jam }} Jam
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <hr>
                            @endforeach
                        @else
                            <p class="text-danger">Data jam kompen tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Bagian Bawah: Informasi Jam Kompen -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-warning text-white text-center">
                <h4>Informasi Jam Kompen</h4>
            </div>
            <div class="card-body">
                @if ($jamkompen && $jamkompen->isNotEmpty())
                    @foreach ($jamkompen as $item)
                        <div class="mb-4">
                            <!-- Periode -->
                            <p class="mb-2">
                                <i class="bi bi-calendar3 me-2 text-primary"></i>
                                <strong>Periode:</strong> 
                                <span class="text-secondary">{{ $item->periode->periode_nama }}</span>
                            </p>

                            <!-- Detail Kompen -->
                            <h5 class="text-success">
                                <i class="bi bi-list-task me-2"></i> Detail Kompen:
                            </h5>
                            <ul class="list-group">
                                @foreach ($item->detail_jamKompen as $detail)
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-bookmark-fill text-info me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <p class="mb-1">
                                                <strong>Mata Kuliah:</strong> 
                                                {{ $detail->matkul->matkul_nama ?? 'Tidak Diketahui' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>Jam:</strong> {{ $detail->jumlah_jam }} Jam
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <hr class="text-muted">
                    @endforeach
                @else
                    <p class="text-danger text-center">
                        <i class="bi bi-exclamation-circle"></i> Data jam kompen tidak tersedia.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

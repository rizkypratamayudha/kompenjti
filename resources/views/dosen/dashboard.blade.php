@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Informasi Dosen -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header text-primary text-center" style="background-color: white;">
                    <h4>Informasi Dosen</h4>
                </div>
                <div class="card-body d-flex flex-column flex-md-row align-items-start gap-4">
                    <div class="text-center">
                        <div class="profile-picture mb-3 mr-4 ml-4">
                            <img id="profile-avatar"
                                src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('user.png') }}"
                                alt="User Avatar"
                                class="img-fluid rounded-circle border border-dark shadow"
                                style="object-fit: cover; width: 120px; height: 120px;">
                        </div>
                        <h5 class="fw-bold text-primary">{{ $user->nama }}</h5>
                    </div>
                    <div class="p-4 rounded flex-grow-1 bg-light border shadow-sm">
                        <p><i class="bi bi-person-circle text-primary"></i>
                            <strong>Username:</strong> {{ $user->username }}
                        </p>
                        <p><i class="bi bi-envelope text-primary"></i>
                            <strong>Email:</strong> {{ $user->detailDosen->email ?? 'Tidak Diketahui' }}
                        </p>
                        <p><i class="bi bi-telephone text-primary"></i>
                            <strong>No HP:</strong> {{ $user->detailDosen->no_hp ?? 'Tidak Diketahui' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Pekerjaan Dosen -->
    <div class="row mt-4">
        @if ($pekerjaan && $pekerjaan->isNotEmpty())
            @foreach ($pekerjaan as $item)
                <div class="col-sm-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $item->pekerjaan_nama }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                <small class="text-muted">Status:</small><br>
                                {{ $item->status }}
                            </p>
                            <p class="card-text">
                                <small class="text-muted">Jumlah Jam Kompen:</small><br>
                                {{ $item->jumlah_jam_kompen }} Jam
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="javascript:void(0)"
                                onclick="modalAction('{{ url('/dosen/' . $item->pekerjaan_id . '/show_ajaxdosen') }}')"
                                    class="btn btn-outline-primary btn-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <i class="fas fa-clock text-muted"></i>
                            Terakhir diperbarui: {{ $item->updated_at->locale('in_id')->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-danger text-center">
                <i class="bi bi-exclamation-circle"></i> Data pekerjaan dosen tidak tersedia.
            </p>
        @endif
    </div>
</div>

<!-- Modal untuk melihat detail pekerjaan -->
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
    data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    // Fungsi untuk memuat modal
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }
</script>
@endpush

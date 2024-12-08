@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Informasi Kaprodi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header text-primary text-center" style="background-color: white;">
                    <h4>Informasi Kaprodi</h4>
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
                            <strong>Email:</strong> {{ $user->detailKaprodi->email ?? 'Tidak Diketahui' }}
                        </p>
                        <p><i class="bi bi-telephone text-primary"></i>
                            <strong>No HP:</strong> {{ $user->detailKaprodi->no_hp ?? 'Tidak Diketahui' }}
                        </p>
                        <p><i class="bi bi-envelope text-primary"></i>
                            <strong>Prodi:</strong> {{ $user->detailKaprodi->prodi->prodi_nama ?? 'Tidak Diketahui' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Permintaan Tanda Tangan  Kaprodi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-bar-chart-line-fill me-3 mr-3 ml-3"></i>Informasi Pending dan Approve Cetak
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center align-items-center">
                        <!-- Informasi Total Pending -->
                        <div class="col-md-4">
                            <div class="py-3">
                                <i class="bi bi-hourglass-split text-info fs-2 mb-2"></i>
                                <h2 class="text-info fw-bold fs-3">{{ $pendingCount }}</h2>
                                <p class="text-muted mb-0">Total Pending Cetak</p>
                            </div>
                        </div>
                        <!-- Informasi Total Approve -->
                        <div class="col-md-4">
                            <div class="py-3">
                                <i class="bi bi-check-circle-fill text-success fs-2 mb-2"></i>
                                <h2 class="text-success fw-bold fs-3">{{ $approveCount }}</h2>
                                <p class="text-muted mb-0">Total Approve Cetak</p>
                            </div>
                        </div>
                        <!-- Pie Chart -->
                        <div class="col-md-4">
                            <div class="py-3">
                                <canvas id="pieChart" style="max-width: 200px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Tambahkan Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk Chart.js
    const ctx = document.getElementById('pieChart').getContext('2d');
    const chartData = {!! json_encode($chartData) !!};

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: ['#2196f3', '#4caf50'], // Warna chart (Pending, Approve)
                hoverBackgroundColor: ['#64b5f6', '#66bb6a'] // Warna ketika di-hover
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>
@endsection

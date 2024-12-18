@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Informasi Statistik -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-circle bg-primary text-white mr-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h5>Total Mahasiswa</h5>
                            <h3 class="text-primary font-weight-bold">{{ $totalMahasiswa }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-circle bg-success text-white mr-3">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div>
                            <h5>Total Dosen/Tendik</h5>
                            <h3 class="text-success font-weight-bold">{{ $totalDosenTendik }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-circle bg-warning text-white mr-3">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <div>
                            <h5>Total Kaprodi</h5>
                            <h3 class="text-warning font-weight-bold">{{ $totalKaprodi }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="icon-circle bg-danger text-white mr-3">
                            <i class="fas fa-briefcase fa-2x"></i>
                        </div>
                        <div>
                            <h5>Total Pekerjaan</h5>
                            <h3 class="text-danger font-weight-bold">{{ $totalPekerjaan }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Kompen -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="icon-circle bg-danger text-white mr-3">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <div>
                        <h5>Mahasiswa Belum Kompen</h5>
                        <h3 class="text-danger font-weight-bold">{{ $mahasiswaBelumKompen }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="icon-circle bg-success text-white mr-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h5>Mahasiswa Sudah Kompen</h5>
                        <h3 class="text-success font-weight-bold">{{ $mahasiswaSudahKompen }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .card-body h5 {
        margin-bottom: 5px;
        font-size: 16px;
        font-weight: 600;
    }
    .card-body h3 {
        margin-top: 0;
        font-size: 28px;
    }
</style>

<!-- Pie Chart in a Full Card with Yellow Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="bi bi-pie-chart-fill me-3 mr-3 ml-3"></i>Level Pengguna Dan Statistik Kompen
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Distribusi Level Pengguna Chart -->
                    <div class="col-md-6 mb-4 d-flex justify-content-center">
                        <div class="py-3">
                            <canvas id="levelPieChart" style="width: 360px; height: 360px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                    <!-- Status Kompen Chart -->
                    <div class="col-md-6 mb-4 d-flex justify-content-center">
                        <div class="py-3">
                            <canvas id="kompenPieChart" style="width: 360px; height: 360px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data for the first pie chart (Distribusi Level Pengguna)
    const ctxLevel = document.getElementById('levelPieChart').getContext('2d');
    const chartData = {!! json_encode($chartData) !!};

    new Chart(ctxLevel, {
        type: 'pie',
        data: {
            labels: ['Total Mahasiswa', 'Total Dosen/Tendik', 'Total Kaprodi','Total Pekerjaan'], // Example labels for status
            datasets: [{
                data: [{{ $totalMahasiswa }}, {{ $totalDosenTendik }}, {{ $totalKaprodi }},{{ $totalPekerjaan }}], // Use the data from the controller
                backgroundColor: ['#2196f3', '#4caf50', '#ff9800', '#9e9e9e'], // Custom colors
                hoverBackgroundColor: ['#64b5f6', '#66bb6a', '#ffb74d', '#cfd8dc']
            }]
        },
        options: {
            maintainAspectRatio: true,  // Ensure aspect ratio is maintained
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

    // Data for the second pie chart (Status Kompen)
    const ctxKompen = document.getElementById('kompenPieChart').getContext('2d');

    new Chart(ctxKompen, {
        type: 'pie',
        data: {
            labels: ['Belum Kompen', 'Sudah Kompen'], // Example labels for status
            datasets: [{
                data: [{{ $mahasiswaBelumKompen }}, {{ $mahasiswaSudahKompen }}], // Use the dynamic data from the controller
                backgroundColor: ['#f44336', '#4caf50'],
                hoverBackgroundColor: ['#ef9a9a', '#66bb6a']
            }]
        },
        options: {
            maintainAspectRatio: true, // Ensure aspect ratio is maintained
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

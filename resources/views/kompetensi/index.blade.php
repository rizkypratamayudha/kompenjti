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

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-2 control-label col-form-label">Filter Periode:</label>
                        <div class="col-3">
                            <select class="form-control" id="periode_filter" name="periode_filter" required>
                                <option value="">- Semua Periode -</option>
                                <option value="{{ $periodeNama }}" selected>{{ $periodeNama }}</option>
                            </select>
                            <small class="form-text text-muted">Pilih periode kompetensi</small>
                        </div>
                    </div>
                </div>
            </div>

            @if ($kompetensi->isEmpty())
                <div class="text-center">
                    <img src="{{ asset('kompetensi_kosong.png') }}" alt="No Competencies"
                        style="max-width: 200px; margin-bottom: 15px;">
                    <p class="mt-3">Belum ada data kompetensi yang tersedia!!!</p>
                </div>
            @endif

            <div class="row">
                @foreach ($kompetensi as $item)
                    <div class="col-sm-6 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $item->kompetensi_nama }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <small class="text-muted">Pengalaman:</small><br>
                                    {{ $item->pengalaman }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Bukti Kompetensi: 
                                        <a href="{{ $item->bukti }}" target="_blank" class="text-info">Lihat</a>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <i class="fas fa-clock text-muted"></i>
                                Terakhir diperbarui: {{ $item->updated_at->locale('in_ID')->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
            data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        // Event listener for periode filter
        $('#periode_filter').change(function() {
            let selectedPeriode = $(this).val();
            // Handle filter logic here, like an AJAX request to fetch filtered data
            alert('Filter berdasarkan periode: ' + selectedPeriode);
        });
    </script>
@endpush

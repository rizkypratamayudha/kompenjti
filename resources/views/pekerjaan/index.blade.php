@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button class="btn btn-sm mt-1 btn-primary" onclick="modalAction('{{ '/pekerjaan/create_ajax' }}')"><i
                        class="fas fa-plus-circle"></i> Pekerjaan</button>
            </div>
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
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="level_id" name="level_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($pekerjaan as $item)
                                    <option value="{{ $item->jenis_pekerjaan }}">{{ $item->jenis_pekerjaan }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Jenis Pekerjaan</small>
                        </div>
                    </div>
                </div>
            </div>
            {{--
            @if ($pekerjaan->isEmpty())
                <div class="text-center">
                    <img src="{{ asset('pekerjaan_kosong.png') }}" alt="No Tasks"
                        style="max-width: 200px; margin-bottom: 15px;">
                    <p class="mt-3">Belum membuat pekerjaan!!!</p>
                </div>
            @endif --}}
            <div class="card shadow" style="width: 250px; border-radius: 10px; overflow: hidden;">
                <div class="card-header text-white d-flex justify-content-between align-items-start"
                    style="background-color: #00796b; padding: 1rem; position: relative;">
                    <h5 class="margin-bottom:20px;">PMPL 3C 2024 2025</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm text-white" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" style="background: transparent; border: none;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Option 1</a>
                            <a class="dropdown-item" href="#">Option 2</a>
                        </div>
                    </div>
                </div>
                <div class="card-body text-center" style="padding: 2rem 1rem; position: relative;">
                    <p class="mb-1" style="margin-top: 1.5rem;">Irsyad Arif Mashudi</p>
                    <!-- Profile Image -->
                    <img src="{{ asset('user.png') }}" alt="Profile" class="rounded-circle"
                        style="width: 60px; height: 60px; object-fit: cover; border: 2px solid white; position: absolute; top: -30px; left: 50%; transform: translate(-50%, 0);">
                </div>
                <div class="card-footer d-flex justify-content-around" style="padding: 0.75rem 1rem;">
                    <button class="btn btn-link p-0"><i class="fas fa-camera"></i></button>
                    <button class="btn btn-link p-0"><i class="fas fa-folder"></i></button>
                </div>
            </div>



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
    </script>
@endpush

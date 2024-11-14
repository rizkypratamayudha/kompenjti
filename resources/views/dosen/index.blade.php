@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button class="btn btn-sm mt-1 btn-primary" onclick="modalAction('{{ 'dosen/create_ajax' }}')"><i
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
                                @foreach ($tugas as $item)
                                    <option value="{{ $item->jenis_pekerjaan }}">{{ $item->jenis_pekerjaan }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Jenis Pekerjaan</small>
                        </div>
                    </div>
                </div>
            </div>
            @if ($tugas->isEmpty())
                <div class="text-center">
                    <img src="{{ asset('pekerjaan_kosong.png') }}" alt="No Tasks"
                        style="max-width: 200px; margin-bottom: 15px;">
                    <p class="mt-3">Belum membuat pekerjaan!!!</p>
                </div>
            @endif

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

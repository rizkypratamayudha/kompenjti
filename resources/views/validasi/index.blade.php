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
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="level_id" name="level_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($level as $item)
                                    <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Level Pengguna</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @if($user->isEmpty())
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <h5><i class="icon fas fa-ban"></i> Data Kosong atau tidak ada</h5>
                        </div>
                    </div>
                @else
                    @foreach ($user as $user)
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <h5 class="card-header">{{ $user->nama }}</h5>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $user->level->level_nama ?? '-' }}</h5>
                                    <p class="card-text">{{ $user->username }}</p>
                                    <a href="{{ url('/user/' . $user->user_id . '/show_ajax') }}" class="btn btn-primary">Detail Pengguna</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
        $(document).ready(function() {
            $('#level_id').on('change', function() {
                var level_id = $(this).val();

                // Mengirim permintaan AJAX untuk mendapatkan data pengguna
                $.ajax({
                    url: "{{ url('validasi/') }}",
                    type: "GET",
                    data: {
                        level_id: level_id
                    },
                    success: function(data) {
                        // Mengganti konten card dengan data baru
                        $('.card-body').html(data);
                    }
                });
            });
        });
    </script>
@endpush

@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"> 
                <a href="{{ url('/mahasiswa/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel"></i> Export Level</a> 
                <a href="{{ url('/mahasiswa/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file-pdf"></i> Export Level</a>
                <button onclick="modalAction('{{ url('/mahasiswa/import') }}')" class="btn btn-sm btn-info mt-1"><i class="fas fa-file-import"></i> Import Mahasiswa</button>  
                <button onclick="modalAction('{{ url('/mahasiswa/create_ajax') }}')" class="btn btn-sm btn-success mt-1"><i class="fas fa-user-plus"></i> Tambah Mahasiswa</button>
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
                            <select class="form-control" id="username" name="username" required>
                                <option value="">- Semua -</option>
                                @foreach ($user as $item)
                                    <option value="{{ $item->user_id }}">{{ $item->username }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Username</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm" id="table_mahasiswa">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Jam Kompen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
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
        var dataMahasiswa;
        $(document).ready(function() {
            dataMahasiswa = $('#table_mahasiswa').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('mahasiswa/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.username = $('#username').val();
                    }
                },
                columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }, {
                    data: "user.username",
                    className: "",
                    orderable: true,
                    searchable: true
                },{
                    data: "user.nama",
                    className: "",
                    orderable: true,
                    searchable: true
                },{
                    data: "akumulasi_jam",
                    className: "",
                    orderable: false,
                    searchable: false
                },{
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }]
            });
            $('#username').on('change', function() {
                datamMahasiswa.ajax.reload();
            });
        });
    </script>
@endpush
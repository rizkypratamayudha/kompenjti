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
                            <select class="form-control" id="semester_id" name="semester_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($semester as $item)
                                    <option value="{{ $item->semester_id }}">{{ $item->semester }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Periode</small>
                        </div>
                    </div>
                </div>
            </div>
                <table class="table table-bordered table-striped table-hover table-sm" id="table_mahasiswa">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Jam Kompen</th>
                            <th>Semester</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" databackdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
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
                        d.semester_id = $('#semester_id').val();
                    }
                },
                columns: [
                {
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
                    data: "semester.semester",
                    className: "",
                    orderable: true,
                    searchable: true
                },{
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }]
            });
            $('#semester_id').on('change', function() {
                dataMahasiswa.ajax.reload();
            })
        });
    </script>
@endpush
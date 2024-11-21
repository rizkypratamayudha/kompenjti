@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('/kompetensi_admin/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel"></i> Export Kompetensi</a>
            <a href="{{ url('/kompetensi_admin/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file-pdf"></i> Export Kompetensi</a>
            <button onclick="modalAction('{{ url('/kompetensi_admin/import') }}')" class="btn btn-sm btn-info mt-1"><i class="fas fa-file-import"></i> Import Kompetensi</button>
            <button onclick="modalAction('{{ url('/kompetensi_admin/create_ajax') }}')" class="btn btn-sm btn-success mt-1"><i class="fas fa-user-plus"></i> Tambah Kompetensi</button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_kompetensi_admin">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
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
        $(document).ready(function() {
            dataLevel = $('#table_kompetensi_admin').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('kompetensi_admin/list') }}",
                    "dataType": "json",
                    "type": "POST"
                },
                columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }, {
                    data: "kompetensi_nama",
                    className: "",
                    orderable: true,
                    searchable: true
                }, {
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }]
            });
        });
    </script>
@endpush

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
            <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Pekerjaan</th>
                        <th>Disetujui pada</th>
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
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        $(document).ready(function() {
            // Initialize DataTable
            dataUser = $('#table_user').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('riwayatkompen/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.level_id = $('#level_id').val();
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "user.username",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "user.nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },{
                        data: "prodi",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "pekerjaan.pekerjaan_nama",
                        className: "",
                        orderable: false,
                        searchable: false
                    }, {

                        data: "created_at",
                        className: "",
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return moment(data).format(
                            'DD MMMM YYYY HH:mm'); // Format tanggal yang diinginkan
                        }
                    }, {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#level_id').on('change', function() {
                dataUser.ajax.reload();
            });
        });
    </script>
@endpush

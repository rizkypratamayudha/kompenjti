@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{url('kompetensi/create_ajax')}}')" class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i>         Kompetensi</button>
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
                            <select class="form-control" id="periode_id" name="periode_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($periodeNama as $item)
                                    <option value="{{ $item->periode_id }}">{{ $item->periode_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Periode</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table_kompetensi">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Periode</th>
                        <th>Kompetensi Nama</th>
                        <th>Pengalaman</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
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
            dataUser = $('#table_kompetensi').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('kompetensi/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.periode_id = $('#periode_id').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "user.periode.periode_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "kompetensi_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "pengalaman",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "bukti",
                        className: "",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#periode_id').on('change', function() {
                dataUser.ajax.reload();
            });
        });
    </script>
@endpush

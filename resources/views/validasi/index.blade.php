@extends('layouts.template')

@section('content')
    @if($user->isEmpty())
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $page->title }}</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Data Kosong atau tidak ada</h5>
                </div>
            </div>
        </div>
    @else
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $page->title }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($user as $user)
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <h5 class="card-header">{{ $user->nama }}</h5>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $user->level->level_nama ?? '-' }}</h5>
                                    <p class="card-text">{{ $user->username }}</p>
                                    <a href="#" class="btn btn-primary">Detail Pengguna</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection

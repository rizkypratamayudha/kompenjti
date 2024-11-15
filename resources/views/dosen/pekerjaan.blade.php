@extends('layouts.template')

@section('content')
    <div class="card-header">
        <h3 class="card-title">
            {{ $pekerjaan->pekerjaan_nama }}
        </h3>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card text-center">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'progres' ? 'active' : '' }}" href="#">Progress</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'anggota' ? 'active' : '' }}" href="#">Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'pelamaran' ? 'active' : '' }}" href="#" >Pelamaran</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
@endsection

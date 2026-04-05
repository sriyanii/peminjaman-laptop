@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-icon', 'fas fa-user')
@section('header-title', 'Profil Saya')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card-custom text-center">
            <div class="user-avatar mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <h4 class="mt-3">{{ auth()->user()->name }}</h4>
            <p class="text-muted">{{ auth()->user()->email }}</p>
            <span class="role-badge {{ auth()->user()->role }}">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title">Informasi Akun</h5>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Bergabung:</strong> {{ auth()->user()->created_at->format('d M Y') }}</p>
                    <p><strong>Terakhir Login:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
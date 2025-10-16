@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/login.js') }}"></script>
@endpush

@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow-sm p-4">
        <h2 class="text-center mb-4">Connexion</h2>

        {{-- Messages d'erreur --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Message de succès --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulaire de connexion --}}
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email"
                       name="email"
                       id="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       required
                       autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control"
                       required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
        </form>

        <p class="text-center mt-3">
            Pas encore inscrit ?
            <a href="{{ route('register.form') }}" class="text-decoration-none">Créer un compte</a>
        </p>
    </div>
</div>
@endsection

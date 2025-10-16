<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>e-Mercerie</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Emplacement pour les styles spécifiques à certaines pages --}}
    @stack('styles')

    {{-- Emplacement pour les scripts spécifiques à certaines pages --}}
    @stack('scripts')
</head>
<body>
    <header class="bg-primary text-white p-3 mb-4">
        <nav class="container d-flex justify-content-between align-items-center">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('supplies.index') }}">Accueil</a></li>

                @guest
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('login.form') }}">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('register.form') }}">Inscription</a></li>
                @else
                    @if(auth()->user()->isCouturier())
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('supplies.selection') }}">Sélection Fournitures</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('orders.index') }}">Mes Commandes</a></li>
                    @elseif(auth()->user()->isMercerie())
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('merchant.supplies.index') }}">Mes Fournitures</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('orders.index') }}">Commandes Reçues</a></li>
                    @endif

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Déconnexion</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </nav>

        @if(session('success'))
            <div class="container mt-2 alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="container mt-2 alert alert-danger">{{ session('error') }}</div>
        @endif
    </header>

    <main class="container">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

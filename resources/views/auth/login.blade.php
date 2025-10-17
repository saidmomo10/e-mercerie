
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

logo
<div class="container" id="container">
    {{-- FORMULAIRE D’INSCRIPTION --}}
    <div class="form-container sign-up-container">
        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            <h1>Créer un compte</h1>

            @if($errors->any() && request()->is('register'))
                <div class="alert error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="text" name="name" placeholder="Nom complet" value="{{ old('name') }}" required />
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input type="password" name="password" placeholder="Mot de passe" required />
            <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required />

            <select name="role" required>
                <option value="">Choisissez un rôle</option>
                <option value="couturier" {{ old('role')=='couturier'?'selected':'' }}>Couturier</option>
                <option value="mercerie" {{ old('role')=='mercerie'?'selected':'' }}>Mercerie</option>
            </select>

            <button type="submit">S'inscrire</button>
        </form>
    </div>

    {{-- FORMULAIRE DE CONNEXION --}}
    <div class="form-container sign-in-container">
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <h1>Connexion</h1>

            @if($errors->any() && request()->is('login'))
                <div class="alert error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input type="password" name="password" placeholder="Mot de passe" required />
            <button type="submit">Se connecter</button>

            <a href="#">Mot de passe oublié ?</a>
        </form>
    </div>

    {{-- OVERLAY ANIMÉ --}}
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Bon retour !</h1>
                <p>Connecte-toi avec tes informations personnelles</p>
                <button class="ghost" id="signIn">Connexion</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Bienvenue !</h1>
                <p>Entre tes informations pour créer un compte</p>
                <button class="ghost" id="signUp">Inscription</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/login.js') }}"></script>


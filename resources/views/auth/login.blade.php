<link rel="stylesheet" href="{{ asset('css/login.css') }}">

<style>
    /* === Palette principale === */
    :root {
        --main-color: #4F0341;
        --accent-color: #7a1761;
        --text-light: #fff;
        --text-dark: #333;
        --bg-light: #f8f8fa;
        --error-color: #e63946;
    }

    body {
        background: var(--bg-light);
        color: var(--text-dark);
    }

    h1 {
        color: var(--main-color);
    }

    /* === Champs de saisie === */
    input, select {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        width: 100%;
        margin-bottom: 15px;
        font-size: 14px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    input:focus, select:focus {
        border-color: var(--main-color);
        box-shadow: 0 0 5px rgba(79, 3, 65, 0.3);
        outline: none;
    }

    /* === Boutons === */
    button {
        background-color: var(--main-color);
        color: var(--text-light);
        border: none;
        border-radius: 25px;
        padding: 12px 25px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    button:hover {
        background-color: var(--accent-color);
        transform: translateY(-2px);
    }

    button.ghost {
        background: transparent;
        border: 2px solid var(--text-light);
        color: var(--text-light);
        border-radius: 25px;
        padding: 10px 25px;
        transition: background-color 0.3s, color 0.3s;
    }

    button.ghost:hover {
        background-color: var(--text-light);
        color: var(--main-color);
    }

    /* === Liens === */
    a {
        color: var(--main-color);
        text-decoration: none;
        font-size: 14px;
    }

    a:hover {
        text-decoration: underline;
    }

    /* === Alertes d'erreurs === */
    .alert.error {
        background-color: #fdecea;
        color: var(--error-color);
        border-left: 4px solid var(--error-color);
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    /* === Container principal === */
    .container {
        background-color: var(--text-light);
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* === Overlay panels === */
    .overlay {
        background: linear-gradient(to right, var(--main-color), var(--accent-color));
    }

    .overlay-panel h1 {
        color: var(--text-light);
    }

    .overlay-panel p {
        color: #f3e9f5;
    }
</style>

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

            <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
        </form>

        @if(session('unverified_email'))
            <div class="mt-3">
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                    <button type="submit" class="btn btn-link">Renvoyer l'email de confirmation</button>
                </form>
            </div>
        @endif
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

<!-- SweetAlert2 for flash messages -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: '{{ session("success") ? "success" : "error" }}',
            title: '{{ session("success") ? "Succès" : "Erreur" }}',
            text: `{{ session('success') ?? session('error') }}`,
            confirmButtonColor: '#4F0341'
        });
    });
</script>
@endif

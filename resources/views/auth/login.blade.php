<link rel="stylesheet" href="{{ asset('css/login.css') }}">

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion – Finger Style</title>
<style>
  @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap");

  /* === RESET === */
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif !important; }

  /* === PALETTE === */
  :root {
      --main-color: #4F0341;
      --accent-color: #9333ea;
      --text-light: #fff;
      --text-dark: #333;
      --bg-light: #f8f8fa;
      --error-color: #e63946;
  }

  body {
      background: linear-gradient(135deg, #4F0341, #9333ea);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: var(--text-dark);
      overflow: hidden;
  }

  /* === CONTAINER PRINCIPAL === */
  .container {
      background-color: var(--text-light);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
      width: 900px;
      display: flex;
      flex-wrap: wrap;
      animation: fadeIn 1s ease;
  }

  /* === FORM SECTION === */
  .form-container {
      flex: 1;
      min-width: 380px;
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      animation: slideInLeft 1.2s ease forwards;
  }

  .form-container h1 {
      font-size: 2rem;
      color: var(--main-color);
      font-weight: 700;
      margin-bottom: 15px;
      opacity:0;
      transform: translateY(20px);
      animation: fadeUp 0.8s forwards 0.2s;
  }

  input, select {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 14px;
      width: 100%;
      margin-bottom: 15px;
      font-size: 14px;
      transition: border-color 0.3s, box-shadow 0.3s;
  }

  input:focus, select:focus {
      border-color: var(--main-color);
      box-shadow: 0 0 8px rgba(79,3,65,0.3);
      outline: none;
  }

  button {
      background-color: var(--main-color);
      color: var(--text-light);
      border: none;
      border-radius: 25px;
      padding: 14px 25px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
  }

  button:hover {
      background-color: var(--accent-color);
      transform: translateY(-2px);
  }

  a {
      color: var(--main-color);
      text-decoration: none;
      font-size: 14px;
  }

  a:hover {
      text-decoration: underline;
  }

  .alert.error {
      background-color: #fdecea;
      color: var(--error-color);
      border-left: 4px solid var(--error-color);
      padding: 10px 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
  }

  .signup-text {
      margin-top: 18px;
      font-size: 14px;
      opacity:0;
      transform: translateY(20px);
      animation: fadeUp 0.8s forwards 1.2s;
  }

  /* === ILLUSTRATION === */
  .illustration {
      flex: 1;
      background: linear-gradient(135deg, #a84aff, #ff3fbf);
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 380px;
      animation: slideInRight 1.2s ease forwards;
  }

  .illustration img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      max-width: 500px;
      /* animation: float 4s ease-in-out infinite; */
  }

  /* === RESPONSIVE === */
  @media (max-width: 850px) {
      .container { flex-direction: column; width: 90%; }
      .illustration { padding: 40px 0; }
  }

  /* === RESPONSIVE === */
  @media (max-width: 1024px) {
      .container { width: 95%; }
      .form-container { padding: 50px 30px; }
  }

  @media (max-width: 850px) {
      .container { flex-direction: column; width: 90%; }
      .form-container { padding: 40px 20px; }
      .illustration { width: 100%; min-height: 200px; margin-top: 20px; }
      .illustration img { width: 80%; max-width: 300px; height: auto; object-fit: contain; }
  }

  @media (max-width: 500px) {
      .form-container h1 { font-size: 1.6rem; }
      input, select { font-size: 13px; padding: 12px; }
      button { padding: 12px 20px; font-size: 14px; }
      .signup-text { font-size: 13px; }
      .options { flex-direction: column; align-items: flex-start; gap: 5px; font-size: 13px; }
  }

  /* === ANIMATIONS === */
  @keyframes fadeIn { from { opacity:0; transform: scale(0.95); } to { opacity:1; transform:scale(1); } }
  @keyframes slideInLeft { from { transform: translateX(-100px); opacity:0; } to { transform: translateX(0); opacity:1; } }
  @keyframes slideInRight { from { transform: translateX(100px); opacity:0; } to { transform: translateX(0); opacity:1; } }
  @keyframes float { 0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);} }
  @keyframes fadeUp { to { opacity:1; transform:translateY(0); } }
</style>
</head>
<body>

<div class="container">
  <!-- Formulaire de connexion -->
  <div class="form-container sign-in-container">
      <form action="{{ route('login.submit') }}" method="POST">
          @csrf
          <h1>Connexion</h1>

          @if($errors->any())
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

          <div style="display:flex;gap:10px;align-items:center;justify-content:space-between;margin-top:10px;">
              <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
              <button type="submit">Se connecter</button>
          </div>
      </form>

      @if(session('unverified_email'))
          <div class="mt-3" style="margin-top:15px;">
              <form method="POST" action="{{ route('verification.resend') }}">
                  @csrf
                  <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                  <button type="submit" style="background:none;border:none;color:var(--main-color);padding:0;">Renvoyer l'email de confirmation</button>
              </form>
          </div>
      @endif

      <p class="signup-text">Pas encore de compte ? <a href="{{ route('register.form') }}">S'inscrire</a></p>
  </div>

  <!-- Illustration -->
  <div class="illustration">
      <img src="{{ asset('images/pexels.jpg') }}" alt="Illustration Connexion">
  </div>
</div>

<script src="{{ asset('js/login.js') }}"></script>

<!-- SweetAlert2 -->
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

</body>
</html>

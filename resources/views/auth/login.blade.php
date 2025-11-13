<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion – Finger Style</title>
<style>
  @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap");

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }

  :root {
    --main-color: #4F0341;
    --accent-color: #9333ea;
    --text-light: #fff;
    --error-color: #e63946;
  }

  body {
    background: linear-gradient(135deg, #4F0341, #9333ea);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
    color: #333;
    overflow-x: hidden; /* ✅ empêche tout débordement horizontal */
  }

  .container {
    background-color: var(--text-light);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 100%;
    max-width: 900px;
    display: flex;
    flex-wrap: wrap;
    animation: fadeIn 1s ease;
  }

  .form-container {
    flex: 1 1 380px;
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: slideInLeft 1.2s ease forwards;
  }

  .form-container h1 {
    font-size: 2rem;
    color: var(--main-color);
    font-weight: 700;
    margin-bottom: 20px;
    animation: fadeUp 0.8s forwards 0.2s;
  }

  input {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 14px;
    width: 100%;
    margin-bottom: 15px;
    font-size: 14px;
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  input:focus {
    border-color: var(--main-color);
    box-shadow: 0 0 8px rgba(79, 3, 65, 0.3);
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

  .illustration {
    flex: 1 1 380px;
    background: linear-gradient(135deg, #a84aff, #ff3fbf);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: slideInRight 1.2s ease forwards;
  }

  .illustration img {
    width: 100%;
    max-width: 450px;
    height: 100%;
    object-fit: cover;
  }

  .signup-text {
    margin-top: 18px;
    font-size: 14px;
  }

  /* ✅ RESPONSIVE */
  @media (max-width: 850px) {
    body { padding: 10px; }
    .container { flex-direction: column; }
    .form-container { padding: 40px 25px; }
    .illustration { order: -1; padding: 30px 0; }
    .illustration img { width: 80%; max-width: 320px; }
  }

  @media (max-width: 500px) {
    .form-container h1 { font-size: 1.6rem; }
    input { font-size: 13px; padding: 12px; }
    button { padding: 12px 20px; font-size: 14px; }
    .signup-text { font-size: 13px; }
    .illustration img {
    width: 100%;
    max-width: 450px;
    height: 100%;
    object-fit: cover;
  }
  }

  @keyframes fadeIn { from { opacity:0; transform:scale(0.95);} to {opacity:1;transform:scale(1);} }
  @keyframes slideInLeft { from {transform:translateX(-100px);opacity:0;} to {transform:translateX(0);opacity:1;} }
  @keyframes slideInRight { from {transform:translateX(100px);opacity:0;} to {transform:translateX(0);opacity:1;} }
  @keyframes fadeUp { to { opacity:1; transform:translateY(0); } }
</style>
</head>
<body>

<div class="container">
  <div class="form-container">
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

      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>

      <div style="display:flex;gap:10px;align-items:center;justify-content:space-between;margin-top:10px;">
        <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
        <button type="submit">Se connecter</button>
      </div>
    </form>

    <p class="signup-text">Pas encore de compte ? <a href="{{ route('register.form') }}">S'inscrire</a></p>
  </div>

  <div class="illustration">
    <img src="{{ asset('images/pexels.jpg') }}" alt="Illustration Connexion">
  </div>
</div>

</body>
</html>

@extends('layouts.app')

@section('content')
<h1>Inscription</h1>

@if($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register.submit') }}" method="POST">
    @csrf
    <div>
        <label>Nom :</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>
    <div>
        <label>Email :</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div>
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>Confirmer mot de passe :</label>
        <input type="password" name="password_confirmation" required>
    </div>
    <div>
        <label>Rôle :</label>
        <select name="role" required>
            <option value="">-- Sélectionner --</option>
            <option value="couturier" {{ old('role')=='couturier'?'selected':'' }}>Couturier</option>
            <option value="mercerie" {{ old('role')=='mercerie'?'selected':'' }}>Mercerie</option>
        </select>
    </div>
    <button type="submit">S'inscrire</button>
</form>
<p>Déjà inscrit ? <a href="{{ route('login.form') }}">Se connecter</a></p>
@endsection

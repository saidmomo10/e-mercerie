@extends('layouts.app')

@section('content')
<h1>Ajouter une fourniture</h1>

@if ($errors->any())
    <div style="color:red">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('merchant.supplies.store') }}" method="POST">
    @csrf

    <label>Fourniture :</label>
    <select name="supply_id" required>
        <option value="">-- Sélectionner --</option>
        @foreach($supplies as $supply)
            <option value="{{ $supply->id }}">{{ $supply->name }}</option>
        @endforeach
    </select>
    <br><br>

    <label>Prix (€) :</label>
    <input type="number" name="price" step="0.01" min="0" required>
    <br><br>

    <label>Quantité en stock :</label>
    <input type="number" name="stock_quantity" min="0" required>
    <br><br>

    <button type="submit">Ajouter</button>
</form>

<a href="{{ route('merchant.supplies.index') }}">Retour à la liste</a>
@endsection

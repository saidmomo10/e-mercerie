@extends('layouts.app')

@section('content')
<h1>Modifier la fourniture</h1>

@if ($errors->any())
    <div style="color:red">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('merchant.supplies.update', $merchantSupply->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Fourniture :</label>
    <input type="text" value="{{ $merchantSupply->supply->name }}" disabled>
    <br><br>

    <label>Prix (€) :</label>
    <input type="number" name="price" step="0.01" min="0" value="{{ $merchantSupply->price }}" required>
    <br><br>

    <label>Quantité en stock :</label>
    <input type="number" name="stock_quantity" min="0" value="{{ $merchantSupply->stock_quantity }}" required>
    <br><br>

    <button type="submit">Mettre à jour</button>
</form>

<a href="{{ route('merchant.supplies.index') }}">Retour à la liste</a>
@endsection

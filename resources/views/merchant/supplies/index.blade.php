@extends('layouts.app')

@section('content')
<h1>Mes Fournitures</h1>

@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif

<a href="{{ route('merchant.supplies.create') }}">Ajouter une nouvelle fourniture</a>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prix</th>
            <th>Quantité en stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($merchantSupplies as $ms)
            <tr>
                <td>{{ $ms->supply->name }}</td>
                <td>{{ $ms->price }} €</td>
                <td>{{ $ms->stock_quantity }}</td>
                <td>
                    <a href="{{ route('merchant.supplies.edit', $ms->id) }}">Modifier</a>
                    <form action="{{ route('merchant.supplies.destroy', $ms->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Aucune fourniture trouvée.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection

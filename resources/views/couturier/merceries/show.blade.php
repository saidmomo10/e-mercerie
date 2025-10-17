@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Fournitures de {{ $mercerie->name }}</h1>

    <form action="{{ route('merceries.preview', $mercerie->id) }}" method="POST">
    @csrf
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Fourniture</th>
                <th>Prix Unitaire (FCFA)</th>
                <th>Stock Disponible</th>
                <th>Quantité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mercerie->merchantSupplies as $supply)
            <tr>
                <td>{{ $supply->supply->name }}</td>
                <td>{{ $supply->price }}</td>
                <td>{{ $supply->stock_quantity }}</td>
                <td>
                    <input type="number" name="items[{{ $loop->index }}][quantity]" 
                           min="0" max="{{ $supply->stock_quantity }}" 
                           class="form-control" value="0">
                    <input type="hidden" name="items[{{ $loop->index }}][merchant_supply_id]" 
                           value="{{ $supply->id }}">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Prévisualiser la commande</button>
    </form>

</div>
@endsection

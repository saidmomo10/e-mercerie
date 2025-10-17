@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Détails de la commande #{{ $order->id }}</h1>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <span><strong>Commande #{{ $order->id }}</strong> - 
                <span class="badge bg-{{ 
                    $order->status === 'pending' ? 'warning' : 
                    ($order->status === 'confirmed' ? 'success' : 'danger')
                }}">
                    {{ ucfirst($order->status) }}
                </span>
            </span>
            <span>Total : <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></span>
        </div>

        <div class="card-body">
            <p><strong>Couturier :</strong> {{ $order->couturier->name }}</p>
            <p><strong>Mercerie :</strong> {{ $order->mercerie->name }}</p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fourniture</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire (FCFA)</th>
                        <th>Sous-total (FCFA)</th>
                        <th>Stock actuel</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->merchantSupply->supply->name ?? $item->merchantSupply->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 0, ',', ' ') }}</td>
                            <td>{{ number_format($item->subtotal, 0, ',', ' ') }}</td>
                            <td>{{ $item->merchantSupply->stock_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(auth()->user()->isMercerie() && $order->status === 'pending')
                <div class="mt-3">
                    <form action="{{ route('orders.accept', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Accepter</button>
                    </form>

                    <form action="{{ route('orders.reject', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Rejeter</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Retour aux commandes</a>
</div>

@endsection

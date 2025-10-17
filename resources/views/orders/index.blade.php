@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Mes commandes</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($orders as $order)
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
        <span>Total : <strong>{{ $order->total_amount }} FCFA</strong></span>
    </div>
    <div class="card-body">
        <p><strong>Mercerie :</strong> {{ $order->mercerie->name }}</p>

        <ul>
            @foreach($order->items as $item)
                <li>
                    {{ $item->merchantSupply->name }} ({{ $item->quantity }} × {{ $item->price }} FCFA)
                </li>
            @endforeach
        </ul>

        @if(auth()->user()->isMercerie() && $order->status === 'pending')
            <form action="{{ route('orders.accept', $order->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Accepter</button>
            </form>

            <form action="{{ route('orders.reject', $order->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Rejeter</button>
            </form>
        @endif

        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">Détails</a>
    </div>
</div>

    @empty
        <p>Aucune commande pour le moment.</p>
    @endforelse
</div>
@endsection

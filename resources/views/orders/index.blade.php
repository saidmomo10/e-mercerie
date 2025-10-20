@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Mes commandes</h1>

    @forelse($orders as $order)
        <div class="card mb-3 mt-5">
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
                @if(auth()->user()->isCouturier())
                <p><strong>{{ $order->mercerie->name }}</strong></p>
                @endif
                <!-- <ul>
                    @foreach($order->items as $item)
                        <li>
                            {{ $item->merchantSupply->name }} ({{ $item->quantity }} × {{ $item->price }} FCFA)
                        </li>
                    @endforeach
                </ul> -->

                @if(auth()->user()->isMercerie() && $order->status === 'pending')
                    <div>
                        <form action="{{ route('orders.accept', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="main-btn success-btn-outline rounded-full btn-hover">Accepter</button>
                        </form>

                        <form action="{{ route('orders.reject', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="main-btn danger-btn-outline rounded-full btn-hover">Rejeter</button>
                        </form>
                    </div>
                @endif

                <a href="{{ route('orders.show', $order->id) }}" class="main-btn primary-btn-outline rounded-full btn-hover mt-5">Détails</a>
            </div>
        </div>

    @empty
        <p>Aucune commande pour le moment.</p>
    @endforelse
</div>
@endsection

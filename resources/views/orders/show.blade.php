@extends('layouts.app')

@section('content')
<div class="tables-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        Détails de la commande #{{ $order->id }}
                    </h4>
                    <span class="badge bg-{{ 
                        $order->status === 'pending' ? 'warning' : 
                        ($order->status === 'confirmed' ? 'success' : 'danger')
                    }} text-dark px-3 py-2">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="border-bottom pb-2 mb-3 d-flex justify-content-between">
                    <div>
                        @if(auth()->user()->isMercerie())
                        <p class="mb-1"><strong>Couturier :</strong> {{ $order->couturier->name }}</p>
                        @else
                        <p class="mb-1"><strong>Mercerie :</strong> {{ $order->mercerie->name }}</p>
                        @endif
                    </div>
                    <div class="text-end">
                        <p class="mb-1"><strong>Total :</strong> 
                            <span class="text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                        </p>
                    </div>
                </div>

                <div class="table-wrapper table-responsive">
                    <table class="table striped-table">
                        <thead>
                            <tr>
                                <th><h6>Fourniture</h6></th>
                                <th><h6>Quantité</h6></th>
                                <th><h6>Prix Unitaire (FCFA)</h6></th>
                                <th><h6>Sous-total (FCFA)</h6></th>
                                <th><h6>Stock actuel</h6></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="min-width">
                                        <p>{{ $item->merchantSupply->supply->name ?? $item->merchantSupply->name }}</p>
                                    </td>
                                    <td><p>{{ $item->quantity }}</p></td>
                                    <td><p>{{ number_format($item->price, 0, ',', ' ') }}</p></td>
                                    <td><p>{{ number_format($item->subtotal, 0, ',', ' ') }}</p></td>
                                    <td><p>{{ $item->merchantSupply->stock_quantity }}</p></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(auth()->user()->isMercerie() && $order->status === 'pending')
                    <div class="mt-4 d-flex gap-3">
                        <form action="{{ route('merchant.orders.accept', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="main-btn success-btn-outline rounded-full btn-hover">
                                <i class="lni lni-checkmark-circle"></i> Accepter
                            </button>
                        </form>

                        <form action="{{ route('merchant.orders.reject', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="main-btn danger-btn-outline rounded-full btn-hover">
                                <i class="lni lni-close"></i> Rejeter
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('orders.index') }}" class="main-btn light-btn btn-hover">
                        <i class="lni lni-arrow-left"></i> Retour aux commandes
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

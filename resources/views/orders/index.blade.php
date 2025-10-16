@extends('layouts.app')

@section('content')
<h1 class="mb-4">Mes commandes</h1>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Mercerie</th>
            <th>Total</th>
            <th>Statut</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->mercerie->name }}</td>
                <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Aucune commande.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection

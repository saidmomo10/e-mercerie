@extends('layouts.app')

@section('content')
<h1 class="mb-4">Comparaison des merceries</h1>

<h2>Merceries disponibles</h2>
<div class="row">
@forelse($disponibles as $mercerie)
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $mercerie['mercerie']['name'] }}</h5>
                <p class="card-text"><strong>Total estimé :</strong> {{ number_format($mercerie['total_estime'], 0, ',', ' ') }} FCFA</p>
                <ul class="list-group list-group-flush mb-3">
                    @foreach($mercerie['details'] as $detail)
                        <li class="list-group-item">
                            {{ $detail['supply'] }} : {{ $detail['quantite'] }} × {{ number_format($detail['prix_unitaire'], 0, ',', ' ') }} = {{ number_format($detail['sous_total'], 0, ',', ' ') }} FCFA
                        </li>
                    @endforeach
                </ul>
                <form action="{{ route('orders.storeFromMerchant', $mercerie['mercerie']['id']) }}" method="POST">
                    @csrf
                    @foreach($mercerie['details'] as $index => $detail)
                        <input type="hidden" name="items[{{ $index }}][merchant_supply_id]" value="{{ $detail['merchant_supply_id'] }}">
                        <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $detail['quantite'] }}">
                    @endforeach
                    <button type="submit" class="btn btn-primary w-100">Valider cette mercerie</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <p>Aucune mercerie disponible pour votre sélection.</p>
@endforelse
</div>

<h2 class="mt-5">Merceries non disponibles</h2>
<div class="row">
@forelse($non_disponibles as $mercerie)
    <div class="col-md-6 mb-3">
        <div class="card border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">{{ $mercerie['mercerie']['name'] }}</h5>
                <ul class="list-group list-group-flush">
                    @foreach($mercerie['raisons'] as $raison)
                        <li class="list-group-item text-danger">{{ $raison }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@empty
    <p>Toutes les merceries sont disponibles pour votre sélection.</p>
@endforelse
</div>
@endsection

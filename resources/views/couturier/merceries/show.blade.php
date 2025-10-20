@extends('layouts.app')

@section('content')
<div class="tables-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Fournitures disponibles chez {{ $mercerie->name }}</h4>
                    <a href="{{ route('supplies.selection') }}" class="main-btn light-btn btn-hover">
                        <i class="lni lni-arrow-left"></i> Retour
                    </a>
                </div>

                <form action="{{ route('merceries.preview', $mercerie->id) }}" method="POST" id="orderForm">
                    @csrf

                    <div class="table-wrapper table-responsive">
                        <table class="table striped-table">
                            <thead>
                                <tr>
                                    <th><h6>Fourniture</h6></th>
                                    <th><h6>Prix Unitaire (FCFA)</h6></th>
                                    <th><h6>Stock Disponible</h6></th>
                                    <th><h6>Quantité</h6></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mercerie->merchantSupplies as $supply)
                                    <tr>
                                        <td class="min-width">
                                            <p>{{ $supply->supply->name }}</p>
                                        </td>
                                        <td>
                                            <p>{{ number_format($supply->price, 0, ',', ' ') }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $supply->stock_quantity }}</p>
                                        </td>
                                        <td>
                                            <input 
                                                type="number" 
                                                name="items[{{ $loop->index }}][quantity]"
                                                min="0" 
                                                max="{{ $supply->stock_quantity }}"
                                                class="form-control quantity-input"
                                                value="0">
                                            <input 
                                                type="hidden" 
                                                name="items[{{ $loop->index }}][merchant_supply_id]" 
                                                value="{{ $supply->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" id="previewBtn" class="main-btn primary-btn btn-hover d-none">
                            <i class="lni lni-eye"></i> Prévisualiser la commande
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- Script pour gérer l’affichage du bouton --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.quantity-input');
    const previewBtn = document.getElementById('previewBtn');

    function toggleButton() {
        // Vérifie si au moins une quantité > 0
        const hasQuantity = Array.from(inputs).some(input => Number(input.value) > 0);
        if (hasQuantity) {
            previewBtn.classList.remove('d-none');
        } else {
            previewBtn.classList.add('d-none');
        }
    }

    // Écoute les changements sur chaque champ quantité
    inputs.forEach(input => {
        input.addEventListener('input', toggleButton);
    });
});
</script>
@endsection

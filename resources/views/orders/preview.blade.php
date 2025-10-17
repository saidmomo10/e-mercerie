@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Prévisualisation de la commande - {{ $mercerie->name }}</h2>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Fourniture</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $item)
            <tr>
                <td>{{ $item['supply'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'], 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($item['subtotal'], 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4 class="text-end mt-3">
        <strong>Total : {{ number_format($total, 0, ',', ' ') }} FCFA</strong>
    </h4>

    <!-- Formulaire de validation -->
    <form id="confirmOrderForm" action="{{ route('merceries.order', $mercerie->id) }}" method="POST">
        @csrf
        @foreach($details as $index => $item)
            <input type="hidden" name="items[{{ $index }}][merchant_supply_id]" value="{{ $item['merchant_supply_id'] ?? '' }}">
            <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
        @endforeach

        <button type="button" id="confirmOrderBtn" class="btn btn-success">Valider la commande</button>
        <a href="{{ route('merceries.show', $mercerie->id) }}" class="btn btn-secondary">Modifier</a>
    </form>
</div>

<!-- Toast de confirmation -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="confirmationToast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        Confirmer la validation de la commande ?
        <div class="mt-2 pt-2 border-top d-flex justify-content-between">
            <button type="button" class="btn btn-light btn-sm me-2" id="confirmYes">Oui, valider</button>
            <button type="button" class="btn btn-outline-light btn-sm" id="confirmNo">Annuler</button>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmOrderBtn');
    const toastEl = document.getElementById('confirmationToast');
    const toast = new bootstrap.Toast(toastEl);
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    const form = document.getElementById('confirmOrderForm');

    confirmBtn.addEventListener('click', function() {
        toast.show();
    });

    confirmYes.addEventListener('click', function() {
        toast.hide();
        form.submit();
    });

    confirmNo.addEventListener('click', function() {
        toast.hide();
    });
});
</script>
@endpush

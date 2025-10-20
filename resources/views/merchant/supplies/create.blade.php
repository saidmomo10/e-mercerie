@extends('layouts.app')

@section('content')

<form action="{{ route('merchant.supplies.store') }}" method="POST">
    @csrf

    <div class="card-style mb-30">
        <h3>Ajouter une fourniture</h3>
        
        <div class="select-style-1 mt-5">
            <label>Fournitures</label>
            <div class="select-position col-lg-6">
                <select name="supply_id" id="supply-select" required>
                    <option value="">Selectionner</option>
                </select>
            </div>
        </div>

        <div class="input-style-1 col-lg-6">
            <label>Prix (€) :</label>
            <input type="number" name="price" step="0.01" min="0" required>
        </div>

        <div class="input-style-1 col-lg-6">
            <label>Quantité en stock :</label>
            <input type="number" name="stock_quantity" min="0" required>
        </div>

        <button class="main-btn dark-btn-outline btn-hover" type="submit">Ajouter</button>
    </div>
</form>

<div class="mt-4">
    <a class="main-btn light-btn btn-hover" href="{{ route('merchant.supplies.index') }}">Retour à la liste</a>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Vérifie que Select2 est disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('#supply-select').select2({
            placeholder: "Rechercher une fourniture...",
            language: "fr",
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('merchant.supplies.search') }}",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            theme: 'default'
        });
    } else {
        console.error('Select2 non chargé');
    }
});
</script>

<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #ddd;
    border-radius: 4px;
    height: 45px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 45px;
    padding-left: 12px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 43px;
}

/* Style pour le dropdown */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #5897fb;
    color: white;
}
</style>
@endpush
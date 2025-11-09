@extends('layouts.app')

@section('content')
<!-- SECTION TITRE AVEC BACKGROUND -->
<div class="page-header">
    <h1>Ajouter une fourniture</h1>
</div>

<div class="add-supply-container">
    <form action="{{ route('merchant.supplies.store') }}" method="POST" class="supply-form">
        @csrf

        <!-- Sélecteur de fourniture -->
        <div class="form-group">
            <label for="supply-select">Fourniture</label>
            <div class="select-wrapper">
                <select name="supply_id" id="supply-select" required>
                    <option value=""> <i class="fa fa-search"></i> Rechercher une fourniture...</option>
                </select>
            </div>
        </div>

        <!-- Prix -->
        <div class="form-group">
            <label for="price">Prix (€)</label>
            <input type="number" name="price" id="price" step="0.01" min="0" placeholder="Ex : 25.00" required>
        </div>

        <!-- Quantité -->
        <div class="form-group">
            <label for="stock_quantity">Quantité en stock</label>
            <input type="number" name="stock_quantity" id="stock_quantity" min="0" placeholder="Ex : 50" required>
        </div>

        <!-- Boutons -->
        <div class="form-actions">
            <button type="submit" class="soft-btn primary-btn">
                <i class="lni lni-plus"></i> Ajouter
            </button>
            <a href="{{ route('merchant.supplies.index') }}" class="soft-btn light-btn">
                <i class="lni lni-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </form>
</div>

<!-- 🎨 STYLE MODERNE -->
<style>
:root {
    --primary-color: #4F0341;
    --secondary-color: #9333ea;
    --gradient: linear-gradient(135deg, #4F0341, #9333ea);
    --white: #fff;
    --gray: #6b7280;
    --radius: 20px;
    --shadow: 0 8px 20px rgba(0,0,0,0.08);
    --transition: all 0.3s ease;
}

/* === TITRE PRINCIPAL === */
.page-header {
    background: var(--primary-color);
    color: var(--white);
    text-align: center;
    padding: 2rem 1rem;
    margin-bottom: 2rem;
    width: 100%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    animation: fadeInDown 0.6s ease;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--white);
    margin: 0;
    letter-spacing: 0.5px;
}

/* === CONTENEUR DU FORMULAIRE === */
.add-supply-container {
    padding: 2.5rem;
    max-width: 700px;
    margin: 0 auto 3rem auto;
    animation: fadeInUp 0.6s ease;
}

/* === FORMULAIRE === */
.supply-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* === CHAMPS === */
.form-group label {
    display: block;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.form-group input, 
.select2-container--modern .select2-selection--single {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 0.8rem 1rem;
    font-size: 1rem;
    transition: var(--transition);
    color: #111827;
    background: rgba(255,255,255,0.9);
}

.form-group input:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(147,51,234,0.15);
    outline: none;
}

/* === SELECT2 PERSONNALISÉ === */
.select-wrapper {
    position: relative;
}
.select2-container--modern .select2-selection--single {
    height: 50px !important;
    display: flex;
    align-items: center;
}
.select2-container--modern .select2-selection--single::before {
    content: "";
    position: absolute;
    left: 10px;
    font-size: 1rem;
    color: var(--gray);
}
.select2-container--modern .select2-selection__rendered {
    padding-left: 2rem !important;
}
.select2-results__option--highlighted {
    background: var(--gradient) !important;
    color: white !important;
}

/* === BOUTONS === */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.soft-btn {
    border: none;
    border-radius: 30px;
    font-weight: 600;
    padding: 0.8rem 2rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    text-align: center;
}

.primary-btn {
    background: var(--gradient);
    color: white;
}
.primary-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(147,51,234,0.3);
}

.light-btn {
    background: #f3f4f6;
    color: var(--primary-color);
}
.light-btn:hover {
    background: #ede9fe;
    transform: translateY(-2px);
}

/* === ANIMATIONS === */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#supply-select').select2({
            theme: 'modern',
            placeholder: " Rechercher une fourniture...",
            language: "fr",
            width: '100%',
            minimumInputLength: 2,
            allowClear: true,
            dropdownCssClass: 'modern-dropdown',
            containerCssClass: 'select2-container--modern',
            ajax: {
                url: "{{ route('merchant.supplies.search') }}",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data.results };
                }
            },
            templateResult: function(supply) {
                if (supply.loading) return "Recherche...";
                return $('<div><strong>' + supply.text + '</strong></div>');
            },
            escapeMarkup: function(markup) { return markup; }
        });
    }
});
</script>
@endpush

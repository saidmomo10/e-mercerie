@extends('layouts.app')

@section('content')
<style>
/* === Styles Modernisés === */

.card-style {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    padding: 2rem 2.5rem;
    max-width: 650px;
    margin: 0 auto;
}

label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
    display: block;
}

input[type="text"],
input[type="number"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input[type="text"]:focus,
input[type="number"]:focus {
    border-color: #6a0b52;
    outline: none;
    box-shadow: 0 0 6px rgba(106, 11, 82, 0.3);
}

.btn-primary-custom {
    background-color: #6a0b52;
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    background-color: #8b166a;
    transform: translateY(-2px);
}

.btn-secondary-custom {
    background-color: #f1f1f1;
    color: #4F0341;
    border: 1px solid #d3d3d3;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-secondary-custom:hover {
    background-color: #e6d9e6;
}

.alert-errors {
    background-color: #fef3f3;
    border-left: 4px solid #dc3545;
    color: #a00;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}
</style>

<!-- === En-tête pleine largeur === -->
<div class="page-title">
    <h1>Modifier la fourniture</h1>
</div>

<div class="card-style">
    @if ($errors->any())
        <div class="alert-errors">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="update-form" action="{{ route('merchant.supplies.update', $merchantSupply->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="supply">Fourniture :</label>
            <input type="text" id="supply" value="{{ $merchantSupply->supply->name }}" disabled>
        </div>

        <div class="mb-3">
            <label for="price">Prix (€) :</label>
            <input type="number" id="price" name="price" step="0.01" min="0" 
                   value="{{ $merchantSupply->price }}" required>
        </div>

        <div class="mb-4">
            <label for="stock_quantity">Quantité en stock :</label>
            <input type="number" id="stock_quantity" name="stock_quantity" min="0" 
                   value="{{ $merchantSupply->stock_quantity }}" required>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('merchant.supplies.index') }}" class="btn btn-secondary-custom">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour
            </a>
            <button type="button" id="submit-btn" class="btn btn-primary-custom">
                <i class="fa-solid fa-save me-1"></i> Mettre à jour
            </button>
        </div>
    </form>
</div>

<!-- === Script SweetAlert2 === -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('update-form');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Confirmer la mise à jour ?',
            text: "Les informations de la fourniture seront modifiées.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4F0341',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, mettre à jour',
            cancelButtonText: 'Annuler',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection

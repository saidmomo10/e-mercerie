@extends('layouts.app')

@section('content')

<h1 class="mb-4">Sélection des fournitures</h1>

<div class="header-search d-none d-md-flex mb-3 position-relative">
    <input type="text" id="search-live" class="form-control" placeholder="Rechercher une fourniture..." autocomplete="off" />

    <!-- Loader (spinner) -->
    <div id="search-loader" 
         class="position-absolute top-50 end-0 translate-middle-y me-3 d-none">
        <div class="spinner-border text-primary" style="width: 1.5rem; height: 1.5rem;" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>
</div>

<form class="mt-4" action="{{ route('merceries.compare') }}" method="POST">
    @csrf
    <div class="row" id="supplies-list">
        @foreach($supplies as $supply)
            <div class="col-md-4 mb-3 supply-card" data-id="{{ $supply->id }}">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $supply->name }}</h5>
                        <p class="card-text">{{ $supply->description }}</p>
                        <div class="mb-2">
                            <label for="quantity_{{ $supply->id }}" class="form-label">Quantité</label>
                            <input type="number" min="0" name="items[{{ $supply->id }}][quantity]" id="quantity_{{ $supply->id }}" class="form-control" value="0">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary mt-3">Comparer les merceries</button>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-live');
    const suppliesList = document.getElementById('supplies-list');
    const loader = document.getElementById('search-loader');
    const quantities = {};

    // Mémoriser les quantités initiales
    document.querySelectorAll('#supplies-list input[type="number"]').forEach(input => {
        const id = input.dataset.id || input.id.replace('quantity_', '');
        quantities[id] = input.value;
        input.addEventListener('input', function() {
            quantities[this.dataset.id || this.id.replace('quantity_', '')] = this.value;
        });
    });

    // Fonction pour recharger toutes les fournitures
    function loadAllSupplies() {
        loader.classList.remove('d-none');
        fetch(`{{ route('api.supplies.search') }}`)
            .then(response => response.json())
            .then(supplies => renderSupplies(supplies))
            .finally(() => loader.classList.add('d-none'));
    }

    // Fonction pour afficher les fournitures dans la liste
    function renderSupplies(supplies) {
        suppliesList.innerHTML = '';
        if (supplies.length === 0) {
            suppliesList.innerHTML = '<div class="col-12"><div class="alert alert-warning">Aucune fourniture trouvée.</div></div>';
            return;
        }
        supplies.forEach(supply => {
            const qty = quantities[supply.id] || 0;
            suppliesList.innerHTML += `
                <div class="col-md-4 mb-3 supply-card" data-id="${supply.id}">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">${supply.name}</h5>
                            <p class="card-text">${supply.description ?? ''}</p>
                            <div class="mb-2">
                                <label for="quantity_${supply.id}" class="form-label">Quantité</label>
                                <input type="number" min="0" name="items[${supply.id}][quantity]" id="quantity_${supply.id}" class="form-control" value="${qty}" data-id="${supply.id}">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        // Réattacher les événements sur les nouveaux inputs
        document.querySelectorAll('#supplies-list input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                quantities[this.dataset.id || this.id.replace('quantity_', '')] = this.value;
            });
        });
    }

    // Recherche dynamique
    let timer = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const query = searchInput.value.trim();

            // ✅ Si le champ est vide, recharger la liste complète
            if (query.length === 0) {
                loadAllSupplies();
                return;
            }

            loader.classList.remove('d-none');
            fetch(`{{ route('api.supplies.search') }}?search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(supplies => renderSupplies(supplies))
                .catch(error => {
                    console.error('Erreur recherche:', error);
                    suppliesList.innerHTML = '<div class="col-12"><div class="alert alert-danger">Erreur lors de la recherche.</div></div>';
                })
                .finally(() => loader.classList.add('d-none'));
        }, 300);
    });
});
</script>
@endpush
@endsection

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Liste des merceries</h1>

    <!-- Barre de recherche avec spinner -->
    <div class="header-search d-none d-md-flex mb-3 position-relative">
        <input type="text" id="search-merceries" class="form-control" 
               placeholder="Rechercher une mercerie..." autocomplete="off" />

        <div id="merceries-loader" 
             class="position-absolute top-50 end-0 translate-middle-y me-3 d-none">
            <div class="spinner-border text-primary" style="width: 1.5rem; height: 1.5rem;" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>

    <!-- Liste des merceries -->
    <div class="row" id="merceries-list">
        @foreach($merceries as $mercerie)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $mercerie->name }}</h5>
                        <p class="card-text text-muted mb-1">{{ $mercerie->city }}</p>
                        <p class="card-text">{{ $mercerie->phone }}</p>
                        <a href="{{ route('merceries.show', $mercerie->id) }}" class="btn btn-primary btn-sm mt-3">
                            Voir les fournitures
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-merceries');
    const merceriesList = document.getElementById('merceries-list');
    const loader = document.getElementById('merceries-loader');
    let timer = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const query = searchInput.value.trim();
            loader.classList.remove('d-none');

            fetch(`{{ route('api.merceries.search') }}?search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(merceries => renderMerceries(merceries))
                .catch(() => {
                    merceriesList.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">Erreur lors de la recherche.</div>
                        </div>`;
                })
                .finally(() => loader.classList.add('d-none'));
        }, 300);
    });

    function renderMerceries(merceries) {
        merceriesList.innerHTML = '';
        if (merceries.length === 0) {
            merceriesList.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-warning">Aucune mercerie trouvée.</div>
                </div>`;
            return;
        }

        merceries.forEach(mercerie => {
            merceriesList.innerHTML += `
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">${mercerie.name}</h5>
                            <p class="card-text text-muted mb-1">${mercerie.city ?? ''}</p>
                            <p class="card-text">${mercerie.phone ?? ''}</p>
                            <a href="/couturier/merceries/${mercerie.id}" class="btn btn-primary btn-sm mt-3">
                                Voir les fournitures
                            </a>
                        </div>
                    </div>
                </div>`;
        });
    }
});
</script>
@endpush
@endsection

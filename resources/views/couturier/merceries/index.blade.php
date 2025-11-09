@extends('layouts.app')

@section('content')
<div class="merceries-container">
    <h1 class="page-title">Liste des merceries</h1>

    <!-- Barre de recherche -->
    <div class="search-bar position-relative mb-5">
        <input type="text" id="search-merceries" class="search-input" placeholder="Rechercher une mercerie..." autocomplete="off" />
        <div id="merceries-loader" class="loader-container d-none">
            <div class="spinner-border text-primary" style="width: 1.5rem; height: 1.5rem;" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>

    <!-- Liste des merceries -->
    <div class="row" id="merceries-list">
        @forelse($merceries as $mercerie)
            <div class="col-md-4 mb-4 fade-in">
                <div class="mercerie-card">
                    <div class="card-image">
                        <img src="{{ $mercerie->avatar_url ?? asset('images/default-mercerie.jpg') }}" 
                             alt="{{ $mercerie->name }}">
                        <span class="card-badge">Mercerie</span>
                    </div>

                    <div class="card-content">
                        <h5 class="card-title">
                            <i class="bi bi-shop me-2 text-secondary"></i>{{ $mercerie->name }}
                        </h5>

                        <div class="card-info">
                            <p><i class="bi bi-geo-alt-fill me-2"></i>{{ $mercerie->city ?? 'Ville non spécifiée' }}</p>
                            <p><i class="bi bi-telephone-fill me-2"></i>{{ $mercerie->phone ?? 'Non renseigné' }}</p>
                        </div>

                        <a href="{{ route('merceries.show', $mercerie->id) }}" class="soft-btn purple">
                            <i class="bi bi-box-arrow-right me-1"></i> Voir les fournitures
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted mt-4">
                <em>Aucune mercerie trouvée.</em>
            </div>
        @endforelse
    </div>
</div>

<style>
:root {
    --primary-color: #4F0341;
    --secondary-color: #9333ea;
    --background-color: #fff;
    --border-color: #f0f0f0;
    --text-color: #2d2d2d;
    --light-text: #777;
}

/* --- STRUCTURE GÉNÉRALE --- */
.merceries-container {
    max-width: 1100px;
    margin: 2.5rem auto;
    padding: 1rem;
    color: var(--text-color);
}

.page-title {
    text-align: center;
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

/* --- BARRE DE RECHERCHE --- */
.search-bar {
    max-width: 600px;
    margin: 0 auto;
}

.search-input {
    width: 100%;
    border-radius: 50px;
    border: 2px solid var(--border-color);
    padding: 0.8rem 1.2rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    outline: none;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
}

.search-input:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
}

.loader-container {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

/* --- CARTE MERCERIE --- */
.mercerie-card {
    background: var(--background-color);
    border-radius: 18px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.mercerie-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 22px rgba(147, 51, 234, 0.2);
}

/* --- IMAGE --- */
.card-image {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.mercerie-card:hover img {
    transform: scale(1.05);
}

.card-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--secondary-color);
    color: #fff;
    padding: 0.35rem 0.9rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* --- CONTENU --- */
.card-content {
    padding: 1.2rem 1.5rem;
}

.card-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--primary-color);
    margin-bottom: 0.8rem;
}

.card-info {
    color: var(--light-text);
    font-size: 0.95rem;
    margin-bottom: 1.2rem;
}

.card-info i {
    color: var(--secondary-color);
}

/* --- BOUTON --- */
.soft-btn {
    display: block;
    width: 100%;
    padding: 0.7rem 1.2rem;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.soft-btn.purple {
    background: var(--primary-color);
    color: #fff;
}

.soft-btn.purple:hover {
    background: var(--secondary-color);
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(147, 51, 234, 0.3);
}

/* --- ANIMATION APPARITION --- */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeInUp 0.6s ease forwards;
}

.fade-in:nth-child(1) { animation-delay: 0.1s; }
.fade-in:nth-child(2) { animation-delay: 0.2s; }
.fade-in:nth-child(3) { animation-delay: 0.3s; }
.fade-in:nth-child(4) { animation-delay: 0.4s; }
.fade-in:nth-child(5) { animation-delay: 0.5s; }
</style>

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

                fetch(`{{ route('api.merceries.search') }}?search=${encodeURIComponent(query)}`, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then(r => {
                        if (!r.ok) throw new Error(`HTTP ${r.status}`);
                        return r.json();
                    })
                    .then(renderMerceries)
                    .catch(() => {
                    merceriesList.innerHTML = `
                        <div class="col-12 text-center mt-3">
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
                <div class="col-12 text-center mt-3">
                    <div class="alert alert-warning">Aucune mercerie trouvée.</div>
                </div>`;
            return;
        }

        merceries.forEach(mercerie => {
            merceriesList.innerHTML += `
                <div class="col-md-4 mb-4 fade-in">
                    <div class="mercerie-card">
                        <div class="card-image">
                            <img src="${mercerie.avatar_url || '/images/default-mercerie.jpg'}" alt="${mercerie.name}">
                            <span class="card-badge">Mercerie</span>
                        </div>
                        <div class="card-content">
                            <h5 class="card-title">
                                <i class="bi bi-shop me-2 text-secondary"></i>${mercerie.name}
                            </h5>
                            <div class="card-info">
                                <p><i class="bi bi-geo-alt-fill me-2"></i>${mercerie.city ?? 'Ville non spécifiée'}</p>
                                <p><i class="bi bi-telephone-fill me-2"></i>${mercerie.phone ?? 'Non renseigné'}</p>
                            </div>
                            <a href="/couturier/merceries/${mercerie.id}" class="soft-btn purple">
                                <i class="bi bi-box-arrow-right me-1"></i> Voir les fournitures
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

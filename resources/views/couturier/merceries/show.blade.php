@extends('layouts.app')

@section('content')
<!-- 🔹 En-tête principale pleine largeur -->
<div class="page-title text-center py-4">
    <h1>{{ $mercerie->name }}</h1>
</div>

<div class="supplies-container">
    <!-- Barre d’action -->
    <div class="header-section">
        <a href="{{ route('supplies.selection') }}" class="soft-btn light">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Barre de recherche -->
    <div class="search-wrapper">
        <div class="search-bar">
            <i class="fa fa-search"></i>
            <input type="text" id="search-live" placeholder="Rechercher une fourniture..." autocomplete="off" />
            <div id="search-loader" class="loader hidden"></div>
        </div>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('merceries.preview', $mercerie->id) }}" method="POST" id="orderForm">
        @csrf

        <div class="supplies-list" id="supplies-list">
            @foreach($mercerie->merchantSupplies as $supply)
                <div class="supply-card" data-id="{{ $supply->id }}">
                    <div class="supply-image">
                        <img src="{{ $supply->supply->image_url ?? asset('images/default.png') }}" alt="{{ $supply->supply->name }}">
                    </div>

                    <div class="supply-content">
                        <h3>{{ $supply->supply->name }}</h3>
                        <p class="description">
                            {{ Str::limit($supply->supply->description ?? 'Aucune description disponible.', 100) }}
                        </p>

                        <div class="price-stock mb-3">
                            <div class="price">
                                <span class="amount">{{ number_format($supply->price, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="stock">
                                <i class="bi bi-box-seam"></i> {{ $supply->stock_quantity }} en stock
                            </div>
                        </div>
                        @if($supply->stock_quantity > 0 && auth()->user()->isCouturier())
                        <div class="quantity-group">
                            <label for="quantity_{{ $supply->id }}">Quantité</label>
                            <input 
                                type="number" 
                                min="0" 
                                max="{{ $supply->stock_quantity }}" 
                                name="items[{{ $loop->index }}][quantity]" 
                                id="quantity_{{ $supply->id }}" 
                                class="quantity-input" 
                                value="0"
                            >
                            <input type="hidden" name="items[{{ $loop->index }}][merchant_supply_id]" value="{{ $supply->id }}">
                        </div>
                        
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-primary add-to-cart" 
                                data-id="{{ $supply->id }}" 
                                data-merchant="{{ $mercerie->id }}" 
                                data-name="{{ addslashes($supply->supply->name) }}" 
                                data-price="{{ $supply->price }}">
                                <i class="fa fa-cart-plus me-1"></i> Ajouter au panier
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" id="previewBtn" class="soft-btn submit-btn d-none">
            <i class="bi bi-eye"></i> Prévisualiser la commande
        </button>
    </form>
</div>

<!-- 🌈 STYLE MODERNE -->
<style>
:root {
    --primary-color: #4F0341;
    --secondary-color: #9333ea;
    --gradient: linear-gradient(135deg, #4F0341, #9333ea);
    --white: #ffffff;
    --gray: #6b7280;
    --radius: 20px;
    --shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s ease;
}

/* --- TITRE PLEINE LARGEUR --- */

/* --- CONTAINER --- */
.supplies-container {
    max-width: 1150px;
    margin: 2rem auto;
    padding: 1rem;
}

/* --- HEADER SECTION --- */
.header-section {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 1.8rem;
}

/* --- BARRE DE RECHERCHE --- */
.search-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
}
.search-bar {
    background: var(--white);
    border-radius: 50px;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 500px;
    display: flex;
    align-items: center;
    padding: 0.8rem 1.5rem;
    position: relative;
    transition: var(--transition);
}
.search-bar i {
    color: var(--primary-color);
    font-size: 1rem;
    margin-right: 0.8rem;
}
.search-bar input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 1rem;
    color: #111827;
}
.search-bar input::placeholder { color: #9ca3af; }
.loader {
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--secondary-color);
    border-radius: 50%;
    width: 18px; height: 18px;
    animation: spin 1s linear infinite;
    position: absolute;
    right: 1.2rem;
}
.hidden { display: none; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* --- GRID --- */
.supplies-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    animation: fadeInUp 0.6s ease;
}

/* --- CARDS --- */
.supply-card {
    background: #fff;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
}
.supply-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
}
.supply-image {
    background: var(--gradient);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 180px;
    padding: 1.5rem;
}
.supply-image img {
    width: 80%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.supply-card:hover img { transform: scale(1.05); }
.supply-content {
    padding: 1.5rem;
}
.supply-content h3 {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.3rem;
}
.supply-content .description {
    color: var(--gray);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    min-height: 45px;
}
.price-stock {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--gray);
    font-size: 0.9rem;
}
.amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--secondary-color);
}
.quantity-group {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    gap: 0.6rem;
    align-items: center;
}
.quantity-group label {
    font-size: 0.9rem;
    color: var(--gray);
}
.quantity-group input {
    width: 70px;
    text-align: center;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 0.4rem;
    transition: var(--transition);
}
.quantity-group input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.15);
    outline: none;
}

/* --- BOUTONS --- */
.soft-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.7rem 1.5rem;
    border-radius: 30px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.95rem;
    text-decoration: none;
}
.soft-btn.light {
    background: #f3e8ff;
    color: var(--primary-color);
}
.soft-btn.light:hover {
    background: var(--secondary-color);
    color: #fff;
}
.soft-btn.submit-btn {
    background: var(--primary-color);
    color: #fff;
    margin: 2rem auto 0;
    display: block;
}
.soft-btn.submit-btn:hover {
    background: var(--secondary-color);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
}
.btn-primary {
    background: var(--primary-color);
    border: none;
    border-radius: 10px;
    transition: var(--transition);
}
.btn-primary:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

/* --- ANIMATIONS --- */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- ✅ JS INTACT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.quantity-input');
    const previewBtn = document.getElementById('previewBtn');
    function toggleButton() {
        const hasQuantity = Array.from(inputs).some(input => Number(input.value) > 0);
        if (previewBtn) previewBtn.classList.toggle('d-none', !hasQuantity);
    }

    inputs.forEach(input => input.addEventListener('input', toggleButton));

    // Recherche locale
    const searchInput = document.getElementById('search-live');
    const loader = document.getElementById('search-loader');
    const suppliesList = document.getElementById('supplies-list');
    let searchTimer = null;

    function setLoader(visible) {
        if (!loader) return;
        loader.classList.toggle('hidden', !visible);
    }

    function showEmptyMessage() {
        if (!suppliesList) return;
        const existing = suppliesList.querySelector('.empty-message');
        if (existing) return;
        const el = document.createElement('div');
        el.className = 'empty-message';
        el.textContent = 'Aucune fourniture trouvée.';
        suppliesList.appendChild(el);
    }

    function removeEmptyMessage() {
        if (!suppliesList) return;
        const existing = suppliesList.querySelector('.empty-message');
        if (existing) existing.remove();
    }

    function filterSupplies(query) {
        if (!suppliesList) return;
        const q = (query || '').trim().toLowerCase();
        const cards = Array.from(suppliesList.querySelectorAll('.supply-card'));
        let visibleCount = 0;
        cards.forEach(card => {
            const title = card.querySelector('h3')?.textContent?.toLowerCase() || '';
            const desc = card.querySelector('.description')?.textContent?.toLowerCase() || '';
            const match = q === '' || title.includes(q) || desc.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });
        if (visibleCount === 0) showEmptyMessage(); else removeEmptyMessage();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value || '';
            setLoader(true);
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                filterSupplies(q);
                setLoader(false);
            }, 200);
        });
    }
});
</script>
@endsection

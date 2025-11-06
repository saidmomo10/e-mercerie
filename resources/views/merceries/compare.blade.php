@extends('layouts.app')

@section('content')
<div class="compare-container">
    <h1 class="page-title">Comparaison des merceries</h1>

    <!-- Merceries disponibles -->
    <section class="section">
        <h2 class="section-title">Merceries disponibles</h2>
        <div class="cards-grid">
            @forelse($disponibles as $mercerie)
                <div class="mercerie-card available">
                    <div class="mercerie-header">
                        <h3>{{ $mercerie['mercerie']['name'] }}</h3>
                        <span class="price">{{ number_format($mercerie['total_estime'], 0, ',', ' ') }} FCFA</span>
                    </div>

                    <ul class="details-list">
                        @foreach($mercerie['details'] as $detail)
                            <li>
                                <span class="supply">{{ $detail['supply'] }}</span>
                                <span class="quantity">{{ $detail['quantite'] }} × {{ number_format($detail['prix_unitaire'], 0, ',', ' ') }}</span>
                                <span class="subtotal">{{ number_format($detail['sous_total'], 0, ',', ' ') }} FCFA</span>
                            </li>
                        @endforeach
                    </ul>

                    <form action="{{ route('orders.storeFromMerchant', $mercerie['mercerie']['id']) }}" method="POST">
                        @csrf
                        @foreach($mercerie['details'] as $index => $detail)
                            <input type="hidden" name="items[{{ $index }}][merchant_supply_id]" value="{{ $detail['merchant_supply_id'] }}">
                            <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $detail['quantite'] }}">
                        @endforeach
                        <button type="submit" class="soft-btn purple">
                            Valider cette mercerie
                        </button>
                    </form>
                </div>
            @empty
                <p class="empty-text">Aucune mercerie disponible pour votre sélection.</p>
            @endforelse
        </div>
    </section>

    <!-- Merceries non disponibles -->
    <section class="section">
        <h2 class="section-title unavailable">Merceries non disponibles</h2>
        <div class="cards-grid">
            @forelse($non_disponibles as $mercerie)
                <div class="mercerie-card unavailable">
                    <div class="mercerie-header">
                        <h3>{{ $mercerie['mercerie']['name'] }}</h3>
                        <span class="status-tag">Non disponible</span>
                    </div>
                    <ul class="details-list unavailable-list">
                        @foreach($mercerie['raisons'] as $raison)
                            <li>{{ $raison }}</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="empty-text">Toutes les merceries sont disponibles pour votre sélection.</p>
            @endforelse
        </div>
    </section>
</div>

<style>
/* --- PALETTE --- */
:root {
    --primary-color: #4F0341;
    --secondary-color: #9333ea;
    --background-color: #fff;
    --text-color: #2d2d2d;
    --light-text: #777;
    --border-color: #f0f0f0;
}

/* --- STRUCTURE GÉNÉRALE --- */
.compare-container {
    max-width: 1100px;
    margin: 2rem auto;
    padding: 1rem;
    color: var(--text-color);
}

.page-title {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

/* --- SECTIONS --- */
.section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--secondary-color);
}

.section-title.unavailable {
    color: #e11d48;
}

/* --- GRILLE --- */
.cards-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
}

/* --- CARDS --- */
.mercerie-card {
    background: var(--background-color);
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.mercerie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 22px rgba(147, 51, 234, 0.15);
}

/* Disponible */
/* .mercerie-card.available {
    border-left: 4px solid var(--secondary-color);
} */

/* Non disponible */
/* .mercerie-card.unavailable {
    border-left: 4px solid #e11d48;
    background: #fff7f8;
} */

/* --- HEADER --- */
.mercerie-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.mercerie-header h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color);
}

.price {
    font-weight: 700;
    color: var(--secondary-color);
    font-size: 1rem;
}

.status-tag {
    background: #ffe6e9;
    color: #b91c1c;
    padding: 0.3rem 0.7rem;
    border-radius: 12px;
    font-size: 0.9rem;
}

/* --- LISTE DES DÉTAILS --- */
.details-list {
    list-style: none;
    margin: 0 0 1.5rem 0;
    padding: 0;
}

.details-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.details-list li:last-child {
    border-bottom: none;
}

.details-list .supply {
    font-weight: 500;
    flex: 1;
    color: var(--text-color);
}

.details-list .quantity {
    color: var(--light-text);
    flex: 1;
    text-align: center;
}

.details-list .subtotal {
    font-weight: 600;
    color: var(--primary-color);
    flex: 1;
    text-align: right;
}

/* --- BOUTON --- */
.soft-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    font-size: 0.95rem;
    width: 100%;
    text-align: center;
}

.soft-btn.purple {
    background: var(--primary-color);
    color: var(--background-color);
}

.soft-btn.purple:hover {
    background: var(--secondary-color);
    transform: scale(1.02);
}

/* --- TEXTES --- */
.empty-text {
    text-align: center;
    color: var(--light-text);
    font-style: italic;
    margin-top: 2rem;
}
</style>
@endsection

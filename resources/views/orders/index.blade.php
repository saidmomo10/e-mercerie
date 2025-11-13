@extends('layouts.app')

@section('content')
<div class="orders-container">
    <div class="page-title">
        <h1>Mes commandes</h1>
    </div>

    <!-- 🔍 Barre de recherche -->
    <form method="GET" action="{{ route('orders.index') }}" id="filterForm" class="filter-bar">
        <div class="filter-group">
            <div class="filter-item">
                <label for="search">Recherche</label>
                <input type="text" name="search" id="search" placeholder="Nom, ID ou statut..."
                       value="{{ request('search') }}">
            </div>
            <div class="filter-item">
                <label for="start_date">Du</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="filter-item">
                <label for="end_date">Au</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="soft-btn purple"><i class="fa-solid fa-filter"></i></button>
                <button type="button" id="resetBtn" class="soft-btn outline"><i class="fa-solid fa-rotate-left"></i></button>
            </div>
        </div>
    </form>

    <!-- 🧾 Liste des commandes -->
    <section class="section">
        <div class="cards-grid">
            @forelse($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <h3>Commande #{{ $order->id }}</h3>
                        <div class="order-meta">
                            <span class="date">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            <span class="price">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    <div class="status-line">
                        <span class="status-badge {{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    {{-- Afficher les infos de l'autre partie (pour le couturier => mercerie, pour la mercerie => couturier) --}}
                    @php
                        $viewer = auth()->user();
                        $other = $viewer->isCouturier() ? $order->mercerie : $order->couturier;
                    @endphp

                    @if($other)
                        <div class="other-info d-flex gap-3 align-items-start mb-3" style="border:1px solid var(--border-color); padding:0.75rem; border-radius:12px;">
                            <img src="{{ $other->avatar_url }}" alt="avatar" width="64" class="rounded-circle">
                            <div>
                                <div class="fw-bold">{{ $other->name }}</div>
                                <div class="text-muted small">{{ $other->email }}</div>
                                @if($other->phone)
                                    <div class="small"><i class="fa-solid fa-phone"></i> {{ $other->phone }}</div>
                                @endif
                                @if($other->city || $other->city_id)
                                    <div class="small"><i class="fa-solid fa-location-dot"></i> {{ $other->city }}{{ $other->quarter ? ' — ' . $other->quarter->name : '' }}</div>
                                @endif
                                <!-- @if($other->address)
                                    <div class="small">🏠 {{ Str::limit($other->address, 80) }}</div>
                                @endif -->
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->isMercerie() && $order->status === 'pending')
                        <div class="actions">
                            @if($order->canBeAccepted())
                                <form action="{{ route('merchant.orders.accept', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="soft-btn green">
                                        <i class="fa-solid fa-check"></i> Accepter
                                    </button>
                                </form>
                            @else
                                <span class="badge-warning">
                                    <i class="fa-solid fa-exclamation-triangle"></i> Stock insuffisant
                                </span>
                            @endif

                            <form action="{{ route('merchant.orders.reject', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="soft-btn red">
                                    <i class="fa-solid fa-xmark"></i> Rejeter
                                </button>
                            </form>
                        </div>
                    @endif

                    <a href="{{ route('orders.show', $order->id) }}" class="soft-btn outline mt-3">
                        <i class="fa-solid fa-eye"></i> Voir les détails
                    </a>
                </div>
            @empty
                <p class="empty-text">Aucune commande trouvée.</p>
            @endforelse
        </div>
    </section>

    <!-- Pagination -->
    @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pagination-container">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- 🔁 Script pour reset -->
<script>
document.getElementById('resetBtn').addEventListener('click', function() {
    document.getElementById('search').value = '';
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('filterForm').submit();
});
</script>

<style>
/* --- PALETTE --- */
:root {
    --primary-color: #4F0341;
    --secondary-color: #9333ea;
    --background-color: #fff;
    --text-color: #2d2d2d;
    --light-text: #777;
    --border-color: #f0f0f0;
    --danger-color: #e11d48;
    --success-color: #16a34a;
}

/* --- CONTAINER --- */
.orders-container {
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

/* --- FILTRE --- */
.filter-bar {
    background: var(--background-color);
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    padding: 1.5rem;
    margin-bottom: 2.5rem;
    border: 1px solid var(--border-color);
}

.filter-group {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
}

.filter-item label {
    font-size: 0.9rem;
    color: var(--light-text);
    display: block;
    margin-bottom: 0.3rem;
}

.filter-item input {
    width: 100%;
    padding: 0.6rem;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
    color: var(--text-color);
}

.filter-actions {
    display: flex;
    align-items: end;
    gap: 0.5rem;
}

/* --- SECTION --- */
.section {
    margin-top: 2rem;
}

/* --- GRILLE --- */
.cards-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
}

/* --- CARD --- */
.order-card {
    background: var(--background-color);
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 22px rgba(147,51,234,0.15);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.8rem;
    margin-bottom: 1rem;
}

.order-header h3 {
    color: var(--primary-color);
    font-size: 1.2rem;
    font-weight: 600;
}

.order-meta {
    text-align: right;
}

.order-meta .date {
    display: block;
    font-size: 0.9rem;
    color: var(--light-text);
}

.order-meta .price {
    color: var(--secondary-color);
    font-weight: 700;
    font-size: 1rem;
}

/* --- STATUT --- */
.status-line {
    margin-bottom: 1rem;
}

.status-badge {
    padding: 0.3rem 0.7rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}
.status-badge.confirmed {
    background: #dcfce7;
    color: #166534;
}
.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

/* --- ACTIONS --- */
.actions {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
}

.badge-warning {
    background: #fef9c3;
    color: #854d0e;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.85rem;
}

/* --- BOUTONS --- */
.soft-btn {
    display: inline-block;
    padding: 0.7rem 1.2rem;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    font-size: 0.9rem;
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

.soft-btn.green {
    background: var(--success-color);
    color: #fff;
}
.soft-btn.green:hover {
    background: #15803d;
}

.soft-btn.red {
    background: var(--danger-color);
    color: #fff;
}
.soft-btn.red:hover {
    background: #be123c;
}

.soft-btn.outline {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}
.soft-btn.outline:hover {
    background: var(--primary-color);
    color: #fff;
}

/* --- TEXTES --- */
.merchant-name {
    color: var(--light-text);
    margin-bottom: 1rem;
}

.empty-text {
    text-align: center;
    color: var(--light-text);
    font-style: italic;
    margin-top: 2rem;
}

/* --- PAGINATION --- */
.pagination-container {
    text-align: center;
    margin-top: 2rem;
}

/* --- RESPONSIVE FIX --- */
@media (max-width: 600px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.4rem;
    }

    .order-meta {
        text-align: left;
        width: 100%;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.3rem;
    }

    .order-meta .date,
    .order-meta .price {
        font-size: 0.9rem;
        word-wrap: break-word;
    }

    .order-header h3 {
        font-size: 1rem;
        word-break: break-word;
    }

    .order-card {
        padding: 1.2rem;
    }

    .filter-group {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        justify-content: flex-start;
    }

    .soft-btn {
        width: 100%;
        text-align: center;
    }
}

</style>
@endsection

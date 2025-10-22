@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-primary fw-bold">Mes commandes</h1>

    <!-- Barre de recherche et filtres -->
    <form method="GET" action="{{ route('orders.index') }}" class="mb-4" id="filterForm">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" name="search" id="search" class="form-control shadow-sm"
                       placeholder="Nom, ID ou statut..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label for="start_date" class="form-label">Du</label>
                <input type="date" name="start_date" id="start_date" class="form-control shadow-sm"
                       value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label">Au</label>
                <input type="date" name="end_date" id="end_date" class="form-control shadow-sm"
                       value="{{ request('end_date') }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                </button>

                <button type="button" id="resetBtn" class="btn btn-outline-secondary w-50 shadow-sm" title="Réinitialiser les filtres">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </form>

    @forelse($orders as $order)
        <div class="card mb-4 shadow-sm border-0 rounded-3">
            <div class="card-header d-flex justify-content-between align-items-center bg-light rounded-top-3">
                <span>
                    <strong class="text-dark">Commande #{{ $order->id }}</strong> - 
                    <span class="badge rounded-pill bg-{{ 
                        $order->status === 'pending' ? 'warning' : 
                        ($order->status === 'confirmed' ? 'success' : 'danger')
                    }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </span>
                <span class="text-muted small">
                    {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <strong class="text-dark">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
                </span>
            </div>

            <div class="card-body">
                @if(auth()->user()->isCouturier())
                    <p class="fw-semibold text-secondary mb-2">{{ $order->mercerie->name }}</p>
                @endif

                @if(auth()->user()->isMercerie() && $order->status === 'pending')
    <div class="mt-3">
        @if($order->canBeAccepted())
            <form action="{{ route('merchant.orders.accept', $order->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 me-2">
                    <i class="fa-solid fa-check"></i> Accepter
                </button>
            </form>
        @else
            <span class="badge bg-warning me-2">
                <i class="fa-solid fa-exclamation-triangle"></i> Stock insuffisant
            </span>
        @endif

        <form action="{{ route('merchant.orders.reject', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="fa-solid fa-xmark"></i> Rejeter
            </button>
        </form>
    </div>
@endif

                <a href="{{ route('orders.show', $order->id) }}" 
                   class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-3">
                    <i class="fa-solid fa-eye"></i> Détails
                </a>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center mt-5">
            <i class="fa-solid fa-info-circle"></i> Aucune commande trouvée.
        </div>
    @endforelse

    <!-- Pagination -->
    @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Script pour réinitialiser le formulaire -->
<script>
    document.getElementById('resetBtn').addEventListener('click', function() {
        document.getElementById('search').value = '';
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        document.getElementById('filterForm').submit();
    });
</script>
@endsection

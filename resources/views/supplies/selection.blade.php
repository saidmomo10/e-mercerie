@extends('layouts.app')

@section('content')
<div class="supplies-container">
        <!-- Titre principal -->
        <div class="page-title">
                <h1>Sélection des fournitures</h1>
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
        <form id="compare-form" class="supplies-form" action="{{ route('merceries.compare') }}" method="POST">
                @csrf

                <div class="supplies-list" id="supplies-list">
                        @forelse($supplies as $supply)
                                <div class="supply-card" data-id="{{ $supply->id }}">
                                        <div class="supply-image">
                                                <img src="{{ $supply->image_url ?? asset('images/default.png') }}" alt="{{ $supply->name }}">
                                        </div>

                                        <div class="supply-content">
                                                <h3>{{ $supply->name }}</h3>
                                                <p class="description">{{ $supply->description }}</p>

                                                <div class="price-qty">
                                                        <div class="price">
                                                                <!-- <span class="amount">$09.00</span>
                                                                <span class="label">Neuf seulement</span> -->
                                                        </div>
                                                        <div class="quantity-group">
                                                                <label for="quantity_{{ $supply->id }}">Qté</label>
                                                                <input type="number" min="0" name="items[{{ $supply->id }}][quantity]" id="quantity_{{ $supply->id }}" value="0" />
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        @empty
                                <p class="empty-message">Aucune fourniture disponible pour le moment.</p>
                        @endforelse
                </div>

                <button type="submit" class="soft-btn submit-btn">Comparer les merceries</button>
        </form>
</div>

<!-- 🎨 STYLE MODERNE -->
<style>

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

.search-bar i { color: #4F0341; font-size: 1rem; margin-right: 0.8rem; }
.search-bar input { flex: 1; border: none; outline: none; font-size: 1rem; color: #111827; }
.search-bar input::placeholder { color: #9ca3af; }
.loader { border: 3px solid #f3f3f3; border-top: 3px solid #9333ea; border-radius: 50%; width: 18px; height: 18px; animation: spin 1s linear infinite; position: absolute; right: 1.2rem; }
.hidden { display: none; }

@keyframes spin { 100% { transform: rotate(360deg); } }
@keyframes fadeDown { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

#supplies-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; animation: fadeInUp 0.5s ease; }
.supply-card { background: #fff; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); transition: var(--transition); text-align: center; }
.supply-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1); }
.supply-image { background: var(--gradient); display: flex; justify-content: center; padding: 1.5rem; }
.supply-image img { width: 80%; transition: transform 0.4s ease; }
.supply-card:hover img { transform: translateY(-5px) rotate(-3deg); }
.supply-content { padding: 1.5rem; }
.supply-content h3 { font-weight: 600; margin-bottom: 0.3rem; }
.supply-content .description { color: var(--gray); font-size: 0.9rem; margin-bottom: 0.8rem; min-height: 40px; }
.rating { color: #facc15; margin-bottom: 0.8rem; }
.star { opacity: 0.4; transition: opacity 0.2s; }
.star.filled { opacity: 1; }
.color-options { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1rem; }
.color-dot { width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 2px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s; }
.color-dot:hover { transform: scale(1.2); }
.price-qty { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.amount { font-size: 1.2rem; font-weight: 700; color: #4F0341; }
.price .label { font-size: 0.8rem; color: var(--gray); }
.quantity-group input { width: 70px; text-align: center; border-radius: 10px; border: 1px solid #e5e7eb; padding: 0.4rem; transition: var(--transition); }
.quantity-group input:focus { border-color: #4F0341; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
.add-btn, .submit-btn { background: var(--primary-color); border: none; border-radius: 30px; color: #fff; font-weight: 600; font-size: 1rem; padding: 0.8rem 2rem; cursor: pointer; transition: var(--transition); width: 100%; }
.add-btn:hover, .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(217, 217, 250, 0.4); background: var(--secondary-color);}
.submit-btn { display: block; width: auto; margin: 2rem auto 0; }
.empty-message, .error-message { text-align: center; background: rgba(255, 255, 255, 0.85); padding: 1rem; border-radius: var(--radius); box-shadow: var(--shadow); animation: fadeInUp 0.4s ease; }
</style>

@push('scripts')
<script>
  // expose the API url to the external script
  window.SUPPLIES_SEARCH_URL = "{{ route('api.supplies.search') }}";
</script>
<script src="{{ asset('js/supplies-selection.js') }}?v={{ filemtime(public_path('js/supplies-selection.js')) }}"></script>
@endpush
@endsection

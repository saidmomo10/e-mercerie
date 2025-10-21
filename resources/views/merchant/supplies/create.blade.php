@extends('layouts.app')

@section('content')

<form action="{{ route('merchant.supplies.store') }}" method="POST">
    @csrf

    <div class="card-style mb-30">
        <h3>Ajouter une fourniture</h3>
        
        <div class="select-style-1 mt-5">
            <label class="form-label">Fournitures</label>
            <div class="supply-search-container col-lg-6">
                <select name="supply_id" id="supply-select" required>
                    <option value="">Sélectionner une fourniture...</option>
                </select>
            </div>
        </div>

        <div class="input-style-1 col-lg-6">
            <label>Prix (€) :</label>
            <input type="number" name="price" step="0.01" min="0" required>
        </div>

        <div class="input-style-1 col-lg-6">
            <label>Quantité en stock :</label>
            <input type="number" name="stock_quantity" min="0" required>
        </div>

        <button class="main-btn dark-btn-outline btn-hover" type="submit">Ajouter</button>
    </div>
</form>

<div class="mt-4">
    <a class="main-btn light-btn btn-hover" href="{{ route('merchant.supplies.index') }}">Retour à la liste</a>
</div>

@endsection

@push('styles')
<style>
/* --- Conteneur --- */
.supply-search-container {
  width: 100%;
  max-width: 500px;
  margin: 1rem 0;
}

/* --- Style du champ Select2 (Glass + Modern UI) --- */
.select2-container--modern .select2-selection--single {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(8px);
  border: 1.8px solid rgba(226, 232, 240, 0.9);
  border-radius: 14px;
  height: 52px;
  padding-left: 2.5rem;
  display: flex;
  align-items: center;
  font-size: 1rem;
  color: #333;
  transition: all 0.3s ease;
  box-shadow: 0 1px 5px rgba(0,0,0,0.06);
  position: relative;
}

/* Icône 🔍 dans le champ */
.select2-container--modern .select2-selection--single::before {
  content: "🔍";
  position: absolute;
  left: 12px;
  font-size: 1.1rem;
  color: #94a3b8;
}

/* --- Focus --- */
.select2-container--modern .focus-effect {
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
}

/* --- Texte --- */
.select2-container--modern .select2-selection__rendered {
  color: #334155;
  font-weight: 500;
}

/* --- Placeholder --- */
.select2-container--modern .select2-selection__placeholder {
  color: #a0aec0;
}

/* --- Flèche --- */
.select2-container--modern .select2-selection__arrow b {
  border-color: #64748b transparent transparent transparent;
}

/* --- Dropdown (menu déroulant) --- */
.select2-container--modern .modern-dropdown {
  background: rgba(255,255,255,0.97);
  border-radius: 14px;
  border: 1px solid rgba(226, 232, 240, 0.8);
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  animation: dropdownFade 0.25s ease forwards;
  overflow: hidden;
}

/* Animation dropdown */
@keyframes dropdownFade {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* --- Options --- */
.select2-results__option {
  padding: 12px 18px;
  font-size: 15px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.select2-results__option--highlighted {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
}

/* --- Loader / Message --- */
.select2-container--modern .select2-results__message {
  color: #64748b;
  padding: 14px;
  text-align: center;
}

/* --- Mobile --- */
@media (max-width: 600px) {
  .select2-container--modern .select2-selection--single {
    height: 46px;
    font-size: 0.9rem;
  }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#supply-select').select2({
            theme: 'modern',
            placeholder: "🔍 Rechercher une fourniture...",
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
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * 20) < data.total_count
                        }
                    };
                },
                cache: true
            },
            templateResult: function(supply) {
                if (supply.loading) {
                    return '<div class="text-gray-500 text-sm">Recherche en cours...</div>';
                }
                if (!supply.id) {
                    return supply.text;
                }
                return $(
                    '<div class="select2-result-label">' +
                        '<span>' + supply.text + '</span>' +
                    '</div>'
                );
            },
            templateSelection: function(supply) {
                if (!supply.id) {
                    return supply.text;
                }
                return supply.text;
            },
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        // Effets focus
        $('#supply-select').on('select2:open', function() {
            $('.select2-selection').addClass('focus-effect');
        });

        $('#supply-select').on('select2:close', function() {
            $('.select2-selection').removeClass('focus-effect');
        });
    }
});
</script>
@endpush

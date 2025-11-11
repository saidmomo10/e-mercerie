@extends('layouts.app')

@section('content')
<!-- === TITRE PRINCIPAL === -->
<div class="page-title text-center py-4" style="background: #4F0341; color: #fff;">
  <h1 class="fw-bold m-0">Ajouter une Fourniture</h1>
</div>

<!-- === CONTENU PRINCIPAL === -->
<div class="container-fluid px-3 px-md-5 mt-4">
  <div class="card-style shadow-sm rounded-4 mx-auto" style="max-width: 700px;">
    
    <!-- === ERREURS DE VALIDATION === -->
    @if ($errors->any())
      <div class="alert-errors">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>⚠️ {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- === FORMULAIRE === -->
    <form id="add-supply-form" action="{{ route('merchant.supplies.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label for="supply_id">Fourniture :</label>
        <select name="supply_id" id="supply_id" required>
          <option value="">— Sélectionner une fourniture —</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="price">Prix (FCFA) :</label>
        <input type="number" id="price" name="price" step="0.01" min="0" placeholder="Ex : 2500" required>
      </div>

      <div class="mb-4">
        <label for="stock_quantity">Quantité en stock :</label>
        <input type="number" id="stock_quantity" name="stock_quantity" min="0" placeholder="Ex : 50" required>
      </div>

      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="{{ route('merchant.supplies.index') }}" class="soft-btn secondary-btn">
          <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
        <button type="button" id="submit-btn" class="soft-btn primary-btn">
          <i class="fa-solid fa-plus"></i> Ajouter
        </button>
      </div>
    </form>
  </div>
</div>

<!-- === STYLE MODERNE === -->
<style>
:root {
  --primary-color: #4F0341;
  --secondary-color: #9333ea;
  --white: #fff;
  --gray: #6b7280;
  --radius: 18px;
  --shadow: 0 8px 18px rgba(0,0,0,0.08);
  --transition: all 0.3s ease;
}

/* === CONTENEUR PRINCIPAL === */
.card-style {
  background: var(--white);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 2.5rem;
  transition: var(--transition);
}
.card-style:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.1);
}

/* === FORMULAIRES === */
label {
  font-weight: 600;
  color: #2d2d2d;
  margin-bottom: 6px;
  display: block;
}
input[type="text"],
input[type="number"],
select {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 1rem;
  transition: var(--transition);
}
input:focus,
select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 6px rgba(79, 3, 65, 0.3);
  outline: none;
}

/* === BOUTONS === */
.soft-btn {
  border: none;
  border-radius: 50px;
  font-weight: 600;
  padding: 0.7rem 1.8rem;
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  text-align: center;
  white-space: nowrap;
}
.primary-btn {
  background: var(--primary-color);
  color: var(--white);
  box-shadow: 0 4px 12px rgba(79, 3, 65, 0.2);
}
.primary-btn:hover {
  background: var(--secondary-color);
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(147,51,234,0.3);
}
.secondary-btn {
  background: #e5e7eb;
  color: #111;
  transition: var(--transition);
}
.secondary-btn:hover {
  background: #d1d5db;
}

/* === ALERTES === */
.alert-errors {
  background-color: #fff3f3;
  border-left: 5px solid #dc3545;
  color: #a00;
  padding: 1rem 1.25rem;
  border-radius: 10px;
  margin-bottom: 1.5rem;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .card-style {
    padding: 1.5rem;
  }
  .soft-btn {
    width: 100%;
    justify-content: center;
  }
  .page-title h1 {
    font-size: 1.6rem;
  }
}
</style>

<!-- === SCRIPT SWEETALERT2 + SELECT2 === -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const submitBtn = document.getElementById('submit-btn');
  const form = document.getElementById('add-supply-form');

  submitBtn.addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'Confirmer l\'ajout ?',
      text: "Cette fourniture sera ajoutée à votre stock.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#4F0341',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Oui, ajouter',
      cancelButtonText: 'Annuler',
      customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
      if (result.isConfirmed) form.submit();
    });
  });

  // Initialisation Select2
  if (typeof $.fn.select2 !== 'undefined') {
    $('#supply_id').select2({
      theme: 'bootstrap-5',
      placeholder: 'Rechercher une fourniture...',
      language: 'fr',
      width: '100%',
      minimumInputLength: 2,
      ajax: {
        url: "{{ route('merchant.supplies.search') }}",
        type: 'GET',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results })
      },
      templateResult: supply => supply.loading ? "Recherche..." : $('<div><strong>' + supply.text + '</strong></div>'),
      escapeMarkup: markup => markup
    });
  }
});
</script>
@endsection

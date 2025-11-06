document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('search-live');
  const list = document.getElementById('supplies-list');
  const loader = document.getElementById('search-loader');
  const quantities = {};
  const SEARCH_URL = window.SUPPLIES_SEARCH_URL || '/api/supplies/search';

  // Safety guards
  if (!list) return;

  // Initialize quantities from server-rendered inputs if any
  document.querySelectorAll('#supplies-list input[type="number"][name]').forEach(el => {
    const match = el.name.match(/items\[(\d+)\]\[quantity\]/);
    if (match) quantities[match[1]] = parseInt(el.value, 10) || 0;
  });

  function attachInputHandlers() {
    document.querySelectorAll('#supplies-list input[type="number"]').forEach(inputEl => {
      const id = (inputEl.dataset.id) ? inputEl.dataset.id : (inputEl.id || '').replace('quantity_', '');
      inputEl.dataset.id = id;
      inputEl.name = `items[${id}][quantity]`;
      inputEl.addEventListener('input', function() {
        quantities[id] = parseInt(this.value, 10) || 0;
      });
    });

    // add-btn handler: increment quantity by 1
    document.querySelectorAll('.add-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const card = this.closest('.supply-card');
        if (!card) return;
        const id = card.dataset.id;
        const inputEl = card.querySelector('input[type="number"]');
        const cur = parseInt(quantities[id] || inputEl?.value || 0, 10) || 0;
        quantities[id] = cur + 1;
        if (inputEl) inputEl.value = quantities[id];
      });
    });
  }

  function renderSupplies(supplies) {
    list.innerHTML = '';
    if (!supplies.length) {
      list.innerHTML = `<div class="empty-message">Aucune fourniture trouvée.</div>`;
      return;
    }

    supplies.forEach(supply => {
      const qty = quantities[supply.id] || 0;
      const card = `
        <div class="supply-card" data-id="${supply.id}">
          <div class="supply-image">
            <img src="${supply.image_url ?? '/images/default.png'}" alt="${supply.name}">
          </div>
          <div class="supply-content">
            <h3>${supply.name}</h3>
            <p class="description">${supply.description ?? ''}</p>
            <div class="price-qty">
              <div class="quantity-group">
                <label>Qté</label>
                <input type="number" min="0" value="${qty}" id="quantity_${supply.id}">
              </div>
            </div>
            <button class="add-btn">Ajouter au panier</button>
          </div>
        </div>`;
      list.insertAdjacentHTML('beforeend', card);
    });

    attachInputHandlers();
  }

  function fetchSupplies(query = '') {
    if (loader) loader.classList.remove('hidden');
    // build URL safely
    try {
      const base = new URL(SEARCH_URL, window.location.origin);
      if (query) base.searchParams.set('search', query);
      fetch(base.toString())
        .then(r => r.json())
        .then(renderSupplies)
        .catch(err => {
          console.error('Erreur fetching supplies:', err);
          list.innerHTML = `<div class="error-message">Erreur lors de la recherche.</div>`;
        })
        .finally(() => { if (loader) loader.classList.add('hidden'); });
    } catch (e) {
      console.error('Invalid SEARCH_URL:', SEARCH_URL, e);
      if (loader) loader.classList.add('hidden');
    }
  }

  if (input) {
    input.addEventListener('input', () => {
      const query = input.value.trim();
      clearTimeout(window.searchTimer);
      window.searchTimer = setTimeout(() => fetchSupplies(query), 300);
    });
  }

  // Form submit: inject hidden inputs for positive quantities and validate
  const compareForm = document.getElementById('compare-form');
  if (compareForm) {
    compareForm.addEventListener('submit', function(e) {
      // remove old injected inputs
      document.querySelectorAll('input[data-preserve="true"]').forEach(el => el.remove());

      const entries = Object.entries(quantities).map(([k,v]) => [k, parseInt(v,10)||0]);
      const positive = entries.filter(([id,qty]) => qty > 0);
      if (positive.length === 0) {
        e.preventDefault();
        // Use SweetAlert2 if available for a nicer UI, otherwise fallback to alert()
        const showWarning = () => {
          try {
            if (window.Swal && typeof window.Swal.fire === 'function') {
              window.Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: 'Veuillez renseigner au moins une quantité supérieure à zéro.',
                confirmButtonText: 'OK'
              }).then(() => {
                // focus first quantity input
                const firstInput = document.querySelector('#supplies-list input[type="number"]');
                if (firstInput) firstInput.focus();
              });
            } else {
              alert('Veuillez renseigner au moins une quantité supérieure à zéro.');
              const firstInput = document.querySelector('#supplies-list input[type="number"]');
              if (firstInput) firstInput.focus();
            }
          } catch (err) {
            console.error('Swal show failed:', err);
            alert('Veuillez renseigner au moins une quantité supérieure à zéro.');
          }
        };

        showWarning();
        return false;
      }

      positive.forEach(([id, qty]) => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `items[${id}][quantity]`;
        hidden.value = String(qty);
        hidden.setAttribute('data-preserve', 'true');
        compareForm.appendChild(hidden);
      });
    });
  }

  // initial server-side DOM might already contain inputs; attach handlers and then optionally fetch to refresh
  attachInputHandlers();
  fetchSupplies();
});

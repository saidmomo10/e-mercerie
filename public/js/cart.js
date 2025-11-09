// Simple client-side cart stored in localStorage under key 'em_cart'
(function(){
  const KEY = 'em_cart';

  function readCart(){
    try{
      const raw = localStorage.getItem(KEY);
      return raw ? JSON.parse(raw) : [];
    }catch(e){ console.error('cart read error', e); return []; }
  }
  function saveCart(cart){
    try{ localStorage.setItem(KEY, JSON.stringify(cart)); }catch(e){ console.error('cart save error', e); }
  }

  function findIndex(cart, item){
    return cart.findIndex(c => c.id === item.id && c.merchant_id === item.merchant_id);
  }

  function addToCart(item){
    const cart = readCart();
    const idx = findIndex(cart, item);
    if (idx === -1) {
      cart.push(item);
    } else {
      cart[idx].quantity = Number(cart[idx].quantity || 0) + Number(item.quantity || 0);
    }
    saveCart(cart);
    updateBadge();
  }

  function removeFromCart(id, merchant_id){
    let cart = readCart();
    cart = cart.filter(i => !(i.id == id && i.merchant_id == merchant_id));
    saveCart(cart);
    updateBadge();
    renderCart();
  }

  function clearCart(){ saveCart([]); updateBadge(); renderCart(); }

  function getCount(){
    return readCart().reduce((s,i)=> s + (Number(i.quantity)||0), 0);
  }

  function updateBadge(){
    const el = document.getElementById('cart-count');
    if (!el) return;
    const cnt = getCount();
    el.textContent = cnt; el.dataset.count = cnt;
    el.style.display = cnt > 0 ? 'inline-block' : 'none';
  }

  function renderCart(){
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');
    if (!container) return;
    const cart = readCart();
    container.innerHTML = '';
    if (!cart.length){
      container.innerHTML = '<div class="p-3 text-center text-muted">Le panier est vide.</div>';
      if (totalEl) totalEl.textContent = '0';
      return;
    }

    let total = 0;
    cart.forEach(item => {
      const line = document.createElement('div');
      line.className = 'd-flex align-items-center justify-content-between p-2 border-bottom';
      const left = document.createElement('div');
      left.innerHTML = `<strong>${escapeHtml(item.name)}</strong><br><small class="text-muted">Qté: ${item.quantity}</small>`;
      const right = document.createElement('div');
      const price = Number(item.price || 0) * Number(item.quantity || 0);
      total += price;
      right.innerHTML = `<div class="text-end">${formatPrice(price)}<br><button class="btn btn-sm btn-link text-danger remove-cart" data-id="${item.id}" data-merchant="${item.merchant_id}">Supprimer</button></div>`;
      line.appendChild(left); line.appendChild(right);
      container.appendChild(line);
    });
    if (totalEl) totalEl.textContent = formatPrice(total);

    // attach remove handlers
    container.querySelectorAll('.remove-cart').forEach(btn => {
      btn.addEventListener('click', function(){
        removeFromCart(this.dataset.id, this.dataset.merchant);
      });
    });
  }

  function formatPrice(v){
    if (typeof Intl !== 'undefined') return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(v);
    return v + ' FCFA';
  }

  function escapeHtml(str){ return String(str).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[m]; }); }

  // Public bindings
  window.EmCart = {
    add: function(item){
      if (!item || !item.id) return;
      item.quantity = Number(item.quantity) || 0;
      if (item.quantity <= 0) return;
      addToCart(item);
      // show feedback
      try{
        if (window.Swal && typeof window.Swal.fire === 'function'){
          window.Swal.fire({ icon: 'success', title: 'Ajouté au panier', text: `${item.quantity} × ${item.name}` , timer: 1400, showConfirmButton: false });
        }
      }catch(e){}
      renderCart();
    },
    remove: removeFromCart,
    clear: clearCart,
    list: readCart,
    count: getCount,
    render: renderCart,
  };

  // Init on DOM ready
  document.addEventListener('DOMContentLoaded', function(){
    // bind add buttons (dynamic supplies can also call EmCart.add manually)
    document.body.addEventListener('click', function(e){
      const btn = e.target.closest?.('.add-to-cart');
      if (!btn) return;
      const id = btn.dataset.id;
      const merchant = btn.dataset.merchant;
      const name = btn.dataset.name || btn.dataset.label || '';
      const price = btn.dataset.price || 0;
      // try to find quantity input in the same card
      const card = btn.closest('.supply-card, .mercerie-card, .card');
      let qty = 1;
      if (card){
        const qinput = card.querySelector('input[type="number"]');
        if (qinput) qty = Number(qinput.value) || 0;
      }
      if (!id){ console.warn('add-to-cart missing id'); return; }
      if (qty <= 0){
        try{
          if (window.Swal && typeof window.Swal.fire === 'function'){
            window.Swal.fire({ icon: 'warning', title: 'Quantité requise', text: 'Veuillez indiquer une quantité supérieure à zéro.' });
          } else { alert('Veuillez indiquer une quantité supérieure à zéro.'); }
        }catch(e){ alert('Veuillez indiquer une quantité supérieure à zéro.'); }
        return;
      }
      EmCart.add({ id: id, name: name, quantity: qty, price: price, merchant_id: merchant });
    });

    // cart button open modal
    const cartBtn = document.getElementById('cart-button');
    const cartModalEl = document.getElementById('cartModal');
    if (cartBtn && cartModalEl && typeof bootstrap !== 'undefined'){
      cartBtn.addEventListener('click', function(){
        renderCart();
        const modal = new bootstrap.Modal(cartModalEl);
        modal.show();
      });
    }

    // preview cart -> build a form and post to merchant preview route
    const previewBtn = document.getElementById('preview-cart-btn');
    if (previewBtn) {
      previewBtn.addEventListener('click', function() {
        const cart = readCart();
        if (!cart.length) {
          try { window.Swal && window.Swal.fire({ icon: 'info', title: 'Panier vide', text: 'Votre panier est vide.' }); } catch(e) {}
          return;
        }

        // ensure all items are from the same merchant
        const merchants = [...new Set(cart.map(i => String(i.merchant_id)))];
        if (merchants.length > 1) {
          try { window.Swal && window.Swal.fire({ icon: 'warning', title: 'Plusieurs merceries', text: 'Le panier contient des articles provenant de plusieurs merceries. Prévisualisez une mercerie à la fois.' }); } catch(e) {}
          return;
        }

        const merchantId = merchants[0];

        // build and submit a form to /couturier/merceries/{merchantId}/preview (POST)
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/couturier/merceries/${merchantId}/preview`;

        // csrf token
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const csrfInput = document.createElement('input'); csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = csrf; form.appendChild(csrfInput);

        // append items as items[][merchant_supply_id] and items[][quantity]
        cart.forEach((it, idx) => {
          const mi = document.createElement('input'); mi.type = 'hidden'; mi.name = `items[${idx}][merchant_supply_id]`; mi.value = String(it.id); form.appendChild(mi);
          const qi = document.createElement('input'); qi.type = 'hidden'; qi.name = `items[${idx}][quantity]`; qi.value = String(it.quantity); form.appendChild(qi);
        });

        document.body.appendChild(form);
        form.submit();
      });
    }

    updateBadge(); renderCart();
  });
})();

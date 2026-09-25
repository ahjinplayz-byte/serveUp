(function () {
  'use strict';

  /* =====================
     Configuration / Selectors
  ===================== */
  const SELECTORS = {
    cartModal: '#cart-modal',
    cartOverlay: '#cart-overlay',
    cartItems: '#cart-items',
    cartTotalPrice: '#cart-total-price',
    cartCount: '#cart-count',
    receiptModal: '#receipt-modal',
    receiptOverlay: '#receipt-overlay',
    receiptItems: '#receipt-items',
    receiptTotal: '#receipt-total',
    addButton: '.add-btn',
    checkoutBtn: '.checkout-btn',
    highlightContainer: '#highlight-cards-container'
  };

  const STORAGE_KEY = 'serveup_cart_v1';
  const PLACE_ORDER_URL = window.PLACE_ORDER_URL || 'place_order.php';

  // In-memory cart: array of { id, name, price, quantity }
  let cart = [];

  // Default highlights (used if localStorage has none)
  const DEFAULT_HIGHLIGHTS = [
    { name: "Creamy Latte", price: 120, image: "" },
    { name: "Signature Cappuccino", price: 150, image: "" },
    { name: "Caramel Macchiato", price: 140, image: "" },
  ];

  /* =====================
     Utilities
  ===================== */
  const $ = (sel) => document.querySelector(sel);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));
  function escapeHtml(s) { return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  /* =====================
     Highlights: get & render
  ===================== */
  function getHighlights() {
    const stored = localStorage.getItem("serveup_highlights");
    if (!stored) return DEFAULT_HIGHLIGHTS;
    try {
      const highlights = JSON.parse(stored);
      return Array.isArray(highlights) ? highlights : DEFAULT_HIGHLIGHTS;
    } catch (err) {
      return DEFAULT_HIGHLIGHTS;
    }
  }

  function renderCustomerHighlights() {
    const container = $(SELECTORS.highlightContainer);
    if (!container) return;
    const items = getHighlights();
    container.innerHTML = '';

    items.forEach((item, idx) => {
      const card = document.createElement('div');
      card.className = 'drink-card';

      const imageStyle = item.image
        ? `style="background-image: url('${escapeHtml(item.image)}'); background-size: cover; background-position: center;"`
        : '';

      // Use the COMMON add-btn class so the global delegation picks it up
      card.innerHTML = `
        <div class="drink-image" ${imageStyle}>
          ${!item.image ? "<span>☕</span>" : ""}
        </div>
        <h3>${escapeHtml(item.name)}</h3>
        <div class="drink-bottom">
          <span class="drink-price">₱${Number(item.price).toFixed(2)}</span>
          <button class="add-btn" data-id="highlight-${idx}" data-name="${escapeHtml(item.name)}" data-price="${Number(item.price).toFixed(2)}" aria-label="Add ${escapeHtml(item.name)}">
            <i class="fa-solid fa-plus"></i>
          </button>
        </div>
      `;
      container.appendChild(card);
    });
  }

  /* =====================
     Cart persistence & helpers
  ===================== */
  function loadCart() {
    try {
      const raw = sessionStorage.getItem(STORAGE_KEY);
      cart = raw ? JSON.parse(raw) : [];
    } catch (err) {
      cart = [];
    }
  }

  function saveCart() {
    try {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    } catch (err) {
      console.warn('Failed to save cart', err);
    }
  }

  function findCartItem(id) {
    return cart.find(i => String(i.id) === String(id));
  }

  /* =====================
     Cart operations (single unified addToCart)
     - Accepts either (object) or (name, price) or (id,name,price)
  ===================== */
  function addToCart(payloadOrName, maybePrice) {
    // Normalize call signatures:
    // addToCart({id,name,price}) OR addToCart(name, price) OR addToCart(idOrName, nameOrPrice, price)
    let id, name, price;
    if (typeof payloadOrName === 'object') {
      id = payloadOrName.id ?? payloadOrName.name;
      name = payloadOrName.name || 'Item';
      price = Number(payloadOrName.price || 0);
    } else {
      // payloadOrName is string (name or id)
      if (typeof maybePrice === 'undefined') {
        // Only one argument, treat as name with price 0
        id = payloadOrName;
        name = payloadOrName;
        price = 0;
      } else {
        // addToCart(name, price) or addToCart(id, name, price) (rare)
        if (typeof maybePrice === 'number') {
          // name, price
          id = payloadOrName;
          name = payloadOrName;
          price = Number(maybePrice || 0);
        } else {
          // maybePrice is not number -> ambiguous; handle as name, price
          name = payloadOrName;
          price = Number(maybePrice || 0);
          id = payloadOrName;
        }
      }
    }

    // If existing item: increase qty
    const existing = findCartItem(id);
    if (existing) {
      existing.quantity = Number(existing.quantity || 0) + 1;
    } else {
      // Ensure id is string to avoid JSON parsing quirks
      cart.push({ id: String(id), name: String(name), price: Number(price), quantity: 1 });
    }
    saveCart();
    renderCart();
  }

  function changeQty(id, delta) {
    const it = findCartItem(id);
    if (!it) return;
    it.quantity = Number(it.quantity || 0) + delta;
    if (it.quantity <= 0) {
      removeFromCart(id);
      return;
    }
    saveCart();
    renderCart();
  }

  function removeFromCart(id) {
    cart = cart.filter(i => String(i.id) !== String(id));
    saveCart();
    renderCart();
  }

  function clearCart() {
    cart = [];
    saveCart();
    renderCart();
  }

  function updateCartBadge() {
    const badge = $(SELECTORS.cartCount) || $('#cart-count');
    if (!badge) return;
    const totalCount = cart.reduce((s,i) => s + (i.quantity || 0), 0);
    badge.textContent = String(totalCount);
  }

  /* =====================
     Render cart drawer
  ===================== */
  function renderCart() {
    const container = $(SELECTORS.cartItems) || $('#cart-items');
    const totalEl = $(SELECTORS.cartTotalPrice) || $('#cart-total-price');
    const badge = $(SELECTORS.cartCount) || $('#cart-count');
    if (!container) return;

    container.innerHTML = '';
    if (!cart.length) {
      container.innerHTML = '<div class="cart-empty">Your cart is empty</div>';
      if (totalEl) totalEl.textContent = '₱0';
      if (badge) badge.textContent = '0';
      return;
    }

    let subtotal = 0;
    cart.forEach(item => {
      const itemTotal = (Number(item.price) || 0) * (Number(item.quantity) || 0);
      subtotal += itemTotal;

      const row = document.createElement('div');
      row.className = 'cart-item';
      row.innerHTML = `
        <div class="meta">
          <div style="font-weight:700">${escapeHtml(item.name)}</div>
          <div class="meta-sub" style="color:#8f6b4a; font-size:13px">₱${Number(item.price).toFixed(2)} each</div>
        </div>
        <div style="text-align:right;">
          <div class="qty-controls" style="display:inline-flex; align-items:center; gap:6px; margin-bottom:6px;">
            <button class="small-btn dec" data-id="${escapeHtml(item.id)}">−</button>
            <div style="min-width:26px; text-align:center;">${escapeHtml(String(item.quantity))}</div>
            <button class="small-btn inc" data-id="${escapeHtml(item.id)}">+</button>
          </div>
          <div style="font-weight:700; margin-top:6px;">₱${itemTotal.toFixed(2)}</div>
          <div style="margin-top:6px;"><button class="small-btn remove" data-id="${escapeHtml(item.id)}">Remove</button></div>
        </div>
      `;
      container.appendChild(row);
    });

    // attach handlers
    container.querySelectorAll('.inc').forEach(b => b.addEventListener('click', (e) => changeQty(e.currentTarget.dataset.id, +1)));
    container.querySelectorAll('.dec').forEach(b => b.addEventListener('click', (e) => changeQty(e.currentTarget.dataset.id, -1)));
    container.querySelectorAll('.remove').forEach(b => b.addEventListener('click', (e) => removeFromCart(e.currentTarget.dataset.id)));

    if (totalEl) totalEl.textContent = `₱${subtotal.toFixed(2)}`;
    if (badge) {
      const count = cart.reduce((s,i) => s + (i.quantity || 0), 0);
      badge.textContent = String(count);
    }
  }

  /* =====================
     Receipt rendering
  ===================== */
  function renderReceipt(serverOrder) {
    const receiptContainer = $(SELECTORS.receiptItems) || $('#receipt-items');
    const receiptTotalEl = $(SELECTORS.receiptTotal) || $('#receipt-total');
    if (!receiptContainer) return;

    receiptContainer.innerHTML = '';
    const items = (serverOrder && Array.isArray(serverOrder.items) && serverOrder.items.length) ? serverOrder.items : cart;

    let subtotal = 0;
    items.forEach(it => {
      const qty = Number(it.quantity || it.qty || 1);
      const price = Number(it.price || it.item_price || 0);
      const rowSubtotal = qty * price;
      subtotal += rowSubtotal;

      const row = document.createElement('div');
      row.className = 'receipt-row';
      row.innerHTML = `<div class="name">${escapeHtml(it.name || it.item_name || 'Item')} <span class="qty">x${qty}</span></div><div class="meta">₱${rowSubtotal.toFixed(2)}</div>`;
      receiptContainer.appendChild(row);
    });

    const tax = +(subtotal * 0.12).toFixed(2);
    const total = +(subtotal + tax).toFixed(2);
    if (receiptTotalEl) receiptTotalEl.textContent = `₱${total.toFixed(2)}`;
  }

  /* =====================
     Show / hide modals
  ===================== */
  function isOpen(el) { return el && el.classList.contains('open'); }

  function showCart() {
    const cartModal = $(SELECTORS.cartModal) || $('#cart-modal');
    const cartOverlay = $(SELECTORS.cartOverlay) || $('#cart-overlay') || $('#cartOverlay');
    if (cartModal) cartModal.classList.add('open');
    if (cartOverlay) cartOverlay.classList.add('open');
    renderCart();
  }
  function hideCart() {
    const cartModal = $(SELECTORS.cartModal) || $('#cart-modal');
    const cartOverlay = $(SELECTORS.cartOverlay) || $('#cart-overlay') || $('#cartOverlay');
    if (cartModal) cartModal.classList.remove('open');
    if (cartOverlay) cartOverlay.classList.remove('open');
  }
  function showReceipt() {
    const receiptModal = $(SELECTORS.receiptModal) || $('#receipt-modal');
    const receiptOverlay = $(SELECTORS.receiptOverlay) || $('#receipt-overlay');
    if (receiptModal) receiptModal.classList.add('open');
    if (receiptOverlay) receiptOverlay.classList.add('open');
  }
  function closeReceipt() {
    const receiptModal = $(SELECTORS.receiptModal) || $('#receipt-modal');
    const receiptOverlay = $(SELECTORS.receiptOverlay) || $('#receipt-overlay');
    if (receiptModal) receiptModal.classList.remove('open');
    if (receiptOverlay) receiptOverlay.classList.remove('open');
  }

  // Expose for inline onclick usage
  window.showCart = showCart;
  window.hideCart = hideCart;
  window.closeReceipt = closeReceipt;

  /* =====================
     Checkout flow: sends order to server and shows receipt
     (Simple prompt-based checkout; you can replace with a form)
  ===================== */
  async function checkout() {
    if (!cart.length) {
      alert('Your cart is empty.');
      return;
    }

    const customer_name = prompt('Customer name:');
    if (!customer_name) { alert('Name is required'); return; }
    const customer_phone = prompt('Phone number:');
    if (!customer_phone) { alert('Phone is required'); return; }
    const customer_email = prompt('Email (optional):', '');

    const itemsPayload = cart.map(i => ({ id: i.id, name: i.name, price: i.price, quantity: i.quantity }));
    const payload = { customer_name, customer_phone, customer_email, notes: '', items: itemsPayload };

    // disable checkout buttons while posting
    $$(SELECTORS.checkoutBtn).forEach(b => b.disabled = true);

    try {
      const res = await fetch(PLACE_ORDER_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data && data.success) {
        const serverOrder = data.order || null;
        renderReceipt(serverOrder);
        showReceipt();
        clearCart();
      } else {
        alert((data && data.message) ? data.message : 'Failed to place order');
      }
    } catch (err) {
      console.error('Checkout failed', err);
      alert('Network error placing order.');
    } finally {
      $$(SELECTORS.checkoutBtn).forEach(b => b.disabled = false);
    }
  }
  window.checkout = checkout;

  /* =====================
     Initialization: DOM events & load
  ===================== */
  document.addEventListener('DOMContentLoaded', () => {
    // Delegated listener for all add-btn clicks (menu items and highlights)
    document.body.addEventListener('click', (e) => {
      const btn = e.target.closest(SELECTORS.addButton);
      if (!btn) return;
      e.preventDefault();
      const id = btn.dataset.id || btn.getAttribute('data-id');
      const name = btn.dataset.name || btn.getAttribute('data-name') || findNameFromCard(btn);
      const priceAttr = btn.dataset.price || btn.getAttribute('data-price') || findPriceFromCard(btn) || '0';
      const price = parseFloat(String(priceAttr).replace(/[^\d.]+/g, '')) || 0;
      addToCart({ id: id ?? name, name, price });
      showCart();
    });

    // Handle cart overlay / receipt overlay clicks for closing (if present)
    const cartOverlay = $(SELECTORS.cartOverlay) || $('#cart-overlay');
    const receiptOverlay = $(SELECTORS.receiptOverlay) || $('#receipt-overlay');
    if (cartOverlay) cartOverlay.addEventListener('click', hideCart);
    if (receiptOverlay) receiptOverlay.addEventListener('click', closeReceipt);

    // Attach header close buttons (if any)
    $$('.close-cart').forEach(btn => btn.addEventListener('click', (ev) => {
      const inReceipt = !!btn.closest(SELECTORS.receiptModal);
      if (inReceipt) closeReceipt(); else hideCart();
    }));

    // Attach checkout buttons
    $$(SELECTORS.checkoutBtn).forEach(b => b.addEventListener('click', checkout));

    // Load state and render
    loadCart();
    renderCart();
    updateCartBadge();

    // Render highlights
    renderCustomerHighlights();

    // Close modals on ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        if (isOpen($(SELECTORS.receiptModal))) closeReceipt();
        else if (isOpen($(SELECTORS.cartModal))) hideCart();
      }
    });
  });

  /* =====================
     Debug / Exposure
  ===================== */
  // Expose some debug helpers
  window._serveup_cart = {
    addToCart,
    changeQty,
    removeFromCart,
    clearCart,
    getCart: () => cart.slice()
  };

})();
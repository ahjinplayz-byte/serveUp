let cart = [];

/* =========================================
   LOAD HIGHLIGHTS FROM SERVER VIA AJAX
========================================= */

async function loadHighlightsFromServer() {
  try {
    const response = await fetch("get_highlights.php");
    const data = await response.json();
    return data.highlights || [];
  } catch (error) {
    console.error("Error loading highlights:", error);
    return [];
  }
}

/* =========================================
   CART & RECEIPT HANDLERS
========================================= */

function showCart() {
  document.getElementById("cart-modal")?.classList.add("open");
  document.getElementById("cart-overlay")?.classList.add("open");
  renderCart();
}

function hideCart() {
  document.getElementById("cart-modal")?.classList.remove("open");
  document.getElementById("cart-overlay")?.classList.remove("open");
}

function addToCart(name, price) {
  const existing = cart.find((item) => item.name === name);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({ name, price, quantity: 1 });
  }
  updateCartBadge();
  showCart();
}

function removeFromCart(name) {
  cart = cart.filter((item) => item.name !== name);
  updateCartBadge();
  renderCart();
}

function updateCartBadge() {
  const countElem = document.getElementById("cart-count");
  if (countElem) {
    const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    countElem.textContent = totalCount;
  }
}

function renderCart() {
  const container = document.getElementById("cart-items");
  const totalElem = document.getElementById("cart-total-price");
  if (!container || !totalElem) return;

  if (cart.length === 0) {
    container.innerHTML = "<p>Your cart is empty.</p>";
    totalElem.textContent = "₱0";
    return;
  }

  container.innerHTML = "";
  let total = 0;

  cart.forEach((item) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const div = document.createElement("div");
    div.className = "cart-item";
    div.innerHTML = `
      <div class="cart-item-details">
        <h4>${item.name}</h4>
        <span class="cart-item-price">₱${item.price} x ${item.quantity}</span>
      </div>
      <div>
        <strong>₱${itemTotal}</strong>
        <button onclick="removeFromCart('${item.name.replace(/'/g, "\\'")}')" style="margin-left:8px; border:none; background:none; color:red; cursor:pointer;">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    `;
    container.appendChild(div);
  });

  totalElem.textContent = `₱${total}`;
}

function checkout() {
  if (cart.length === 0) {
    alert("Your cart is empty!");
    return;
  }
  showReceipt();
}

function showReceipt() {
  const receiptItemsContainer = document.getElementById("receipt-items");
  const receiptTotalElem = document.getElementById("receipt-total");

  if (!receiptItemsContainer || !receiptTotalElem) {
    console.error("Receipt elements not found in HTML.");
    return;
  }

  receiptItemsContainer.innerHTML = "";
  let total = 0;

  cart.forEach((item) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const row = document.createElement("div");
    row.className = "receipt-item-row";
    row.style.display = "flex";
    row.style.justifyContent = "space-between";
    row.style.margin = "6px 0";
    row.innerHTML = `
      <span>${item.name} × ${item.quantity}</span>
      <span>₱${itemTotal}</span>
    `;
    receiptItemsContainer.appendChild(row);
  });

  receiptTotalElem.textContent = `₱${total}`;

  // Close cart modal, open receipt modal
  hideCart();
  document.getElementById("receipt-modal")?.classList.add("open");
  document.getElementById("receipt-overlay")?.classList.add("open");

  // Empty cart after receipt creation
  cart = [];
  updateCartBadge();
}

function closeReceipt() {
  document.getElementById("receipt-modal")?.classList.remove("open");
  document.getElementById("receipt-overlay")?.classList.remove("open");
}

/* =========================================
   PAGE INIT
========================================= */

document.addEventListener("DOMContentLoaded", () => {
  updateCartBadge();
});

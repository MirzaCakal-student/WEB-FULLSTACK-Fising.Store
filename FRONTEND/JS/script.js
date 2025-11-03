/* ===================================
   FISHING PLANET - MAIN JAVASCRIPT
   =================================== */

// ✅ Correct path to backend (relative from FRONTEND folder)
const API_BASE = "../BACKEND/rest/ROUTES/";

// Global app state
let appState = {
  products: [],
  cart: [],
  wishlist: [],
  currentUser: { id: 1, name: "Guest" },
};

// ============ UTILITIES ============
function showAlert(message, type = "success") {
  const html = `
    <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
  $("#alert-container").html(html);
  setTimeout(() => $(".alert").alert("close"), 3000);
}

function updateBadges() {
  $("#cart-count").text(appState.cart.length);
  $("#wishlist-count").text(appState.wishlist.length);
}

// ============ PRODUCTS ============
async function fetchProducts() {
  try {
    const res = await fetch(`${API_BASE}ProductRoutes.php`);
    if (!res.ok) throw new Error("Products fetch failed");
    appState.products = await res.json();
    renderProductsGrid();
  } catch (err) {
    console.error("Products fetch error", err);
    showAlert("⚠️ Backend offline, showing demo data", "warning");
  }
}

function renderProductsGrid() {
  const container = $("#productsContainer");
  if (!container.length) return;

  container.html(
    appState.products
      .map(
        (p) => `
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm border-0">
            <img src="${p.image}" class="card-img-top" style="height:220px;object-fit:cover;">
            <div class="card-body text-center">
              <h5>${p.name}</h5>
              <p class="fw-bold text-success">$${p.price}</p>
              <button class="btn btn-success btn-sm me-2" onclick="addToCart(${p.id})">
                <i class="bi bi-cart-plus"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="addToWishlist(${p.id})">
                <i class="bi bi-heart"></i>
              </button>
            </div>
          </div>
        </div>`
      )
      .join("")
  );
}

// ============ CART ============
async function fetchCart() {
  try {
    const res = await fetch(`${API_BASE}CartItemRoutes.php?user_id=${appState.currentUser.id}`);
    if (!res.ok) throw new Error("Cart fetch failed");
    appState.cart = await res.json();
    renderCart();
  } catch (e) {
    console.error("Cart fetch error", e);
    showAlert("⚠️ Backend offline for cart", "warning");
  }
}

async function addToCart(productId) {
  try {
    const response = await fetch(`${API_BASE}CartItemRoutes.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id: appState.currentUser.id,
        product_id: productId,
        quantity: 1,
      }),
    });

    if (!response.ok) throw new Error("Failed to add to cart");
    showAlert("✅ Added to cart!");
    fetchCart();
  } catch (err) {
    console.error("AddToCart error:", err);
    showAlert("Server offline or route not found", "danger");
  }
}

async function removeFromCart(productId) {
  try {
    const res = await fetch(`${API_BASE}CartItemRoutes.php?id=${productId}`, {
      method: "DELETE",
    });

    if (res.ok) {
      showAlert("🗑️ Removed from cart");
      fetchCart();
    } else showAlert("Failed to remove", "danger");
  } catch (err) {
    console.error("RemoveCart error:", err);
    showAlert("Error removing item", "danger");
  }
}

function renderCart() {
  const el = $("#cartContainer");
  if (!el.length) return;

  if (!appState.cart.length) {
    el.html(`<div class="alert alert-info">Your cart is empty.</div>`);
    return;
  }

  let total = 0;
  el.html(`
    <div class="card p-3">
      <table class="table align-middle">
        <thead class="table-primary">
          <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr>
        </thead>
        <tbody>
          ${appState.cart
            .map((p) => {
              const sub = p.price * p.quantity;
              total += sub;
              return `
                <tr>
                  <td>${p.name}</td>
                  <td>${p.quantity}</td>
                  <td>$${p.price.toFixed(2)}</td>
                  <td>$${sub.toFixed(2)}</td>
                  <td><button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${p.id})"><i class="bi bi-trash"></i></button></td>
                </tr>`;
            })
            .join("")}
        </tbody>
      </table>
      <div class="text-end fw-bold mt-2">Total: $${total.toFixed(2)}</div>
    </div>`);
  updateBadges();
}

// ============ WISHLIST ============
async function fetchWishlist() {
  try {
    const res = await fetch(`${API_BASE}WishlistItemRoutes.php?user_id=${appState.currentUser.id}`);
    if (!res.ok) throw new Error("Wishlist fetch failed");
    appState.wishlist = await res.json();
    renderWishlist();
  } catch (err) {
    console.error("Wishlist fetch error", err);
    showAlert("⚠️ Backend offline for wishlist", "warning");
  }
}

async function addToWishlist(productId) {
  try {
    const res = await fetch(`${API_BASE}WishlistItemRoutes.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: appState.currentUser.id, product_id: productId }),
    });
    if (!res.ok) throw new Error("Failed");
    showAlert("❤️ Added to wishlist!");
    fetchWishlist();
  } catch (err) {
    console.error(err);
  }
}

async function removeFromWishlist(productId) {
  try {
    const res = await fetch(`${API_BASE}WishlistItemRoutes.php?id=${productId}`, { method: "DELETE" });
    if (res.ok) {
      showAlert("💔 Removed from wishlist!");
      fetchWishlist();
    } else showAlert("Failed to remove", "danger");
  } catch (err) {
    console.error(err);
  }
}

function renderWishlist() {
  const el = $("#wishlistContainer");
  if (!el.length) return;

  if (!appState.wishlist.length) {
    el.html(`<div class="alert alert-info">Your wishlist is empty.</div>`);
    return;
  }

  el.html(
    `<div class="row">
      ${appState.wishlist
        .map(
          (p) => `
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="${p.image}" class="card-img-top" style="height:220px;object-fit:cover;">
            <div class="card-body text-center">
              <h5>${p.name}</h5>
              <p class="fw-bold text-primary">$${p.price}</p>
              <button class="btn btn-outline-danger btn-sm" onclick="removeFromWishlist(${p.id})"><i class="bi bi-heartbreak"></i> Remove</button>
            </div>
          </div>
        </div>`
        )
        .join("")}
    </div>`
  );
  updateBadges();
}

// Initialize
$(document).ready(() => {
  const hash = window.location.hash;
  if (hash.includes("products")) fetchProducts();
  if (hash.includes("wishlist")) fetchWishlist();
  if (hash.includes("cart")) fetchCart();
  updateBadges();
});

/* ===================================
   FISHING PLANET - MAIN JAVASCRIPT
   Fully Updated – Works With Your Backend
=================================== */

const API_BASE = "../BACKEND/rest/ROUTES/";

/* ================================
   GLOBAL APP STATE
================================ */
let appState = {
    products: [],
    filteredProducts: [],
    cart: [],
    wishlist: [],
    currentUser: { id: 1, name: "Guest" }
};

/* ================================
   UTILITIES
================================ */
function showAlert(message, type = "success") {
    const html = `
        <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $("#alert-container").html(html);
    setTimeout(() => $(".alert").alert("close"), 3000);
}

function updateBadges() {
    $("#cart-count").text(appState.cart.length);
    $("#wishlist-count").text(appState.wishlist.length);
}

/* ================================
   FETCH PRODUCTS (BACKEND)
================================ */
async function fetchProducts() {
    try {
        const res = await fetch(`${API_BASE}ProductRoutes.php`);
        const json = await res.json();

        appState.products = json.data.map(p => ({
            id: p.product_id,
            name: p.name,
            category: p.category ?? "general",
            price: parseFloat(p.price),
            image: p.image_url,
            description: p.description ?? "No description provided",
            stock: p.quantity ?? 0
        }));

        appState.filteredProducts = [...appState.products];

        renderProductsPage();

    } catch (err) {
        console.error("Product fetch error:", err);
        showAlert("Cannot load products (backend offline)", "danger");
    }
}

/* ================================
   PRODUCTS PAGE RENDERING
================================ */
function renderProductsPage() {
    const container = $("#productsContainer");
    if (!container.length) return;

    container.html(
        appState.filteredProducts.map(p => `
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="product-card">
                
                <div class="product-image-container">
                    <img src="${p.image}" class="product-image" onclick="openImageModal('${p.image}')">

                    <button class="wishlist-btn ${isInWishlist(p.id) ? "active" : ""}"
                            onclick="toggleWishlist(${p.id})">
                        <i class="bi bi-heart${isInWishlist(p.id) ? '-fill' : ''}"></i>
                    </button>
                </div>

                <div class="card-body">
                    <div class="product-category">${p.category}</div>
                    <h5 class="product-title">${p.name}</h5>
                    <div class="product-price">$${p.price.toFixed(2)}</div>

                    <div class="quantity-control">
                        <button onclick="changeQuantity(${p.id}, -1)">-</button>
                        <input type="number" id="qty-${p.id}" value="1" min="1">
                        <button onclick="changeQuantity(${p.id}, 1)">+</button>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="addToCart(${p.id})">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>

                        <button class="btn btn-outline-secondary btn-sm" onclick="openProductModal(${p.id})">
                            <i class="bi bi-eye"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `).join("")
    );
}

/* ================================
   PRODUCT SEARCH / FILTER / SORT
================================ */
function filterProducts() {
    const term = $("#searchInput").val().toLowerCase();
    const category = $("#categoryFilter").val();

    appState.filteredProducts = appState.products.filter(p => {
        const matchName = p.name.toLowerCase().includes(term);
        const matchCat = !category || p.category === category;
        return matchName && matchCat;
    });

    sortProducts();
}

function sortProducts() {
    const sortType = $("#sortFilter").val();

    const f = appState.filteredProducts;

    switch (sortType) {
        case "name-asc": f.sort((a, b) => a.name.localeCompare(b.name)); break;
        case "name-desc": f.sort((a, b) => b.name.localeCompare(a.name)); break;
        case "price-asc": f.sort((a, b) => a.price - b.price); break;
        case "price-desc": f.sort((a, b) => b.price - a.price); break;
    }

    renderProductsPage();
}

/* ================================
   PRODUCT MODAL
================================ */
function openProductModal(id) {
    const p = appState.products.find(x => x.id === id);
    if (!p) return;

    $("#modalProductName").text(p.name);

    $("#modalProductContent").html(`
        <div class="row">
            <div class="col-md-6">
                <img src="${p.image}" class="img-fluid rounded" onclick="openImageModal('${p.image}')">
            </div>

            <div class="col-md-6">
                <h3 class="text-primary mb-3">$${p.price.toFixed(2)}</h3>
                <p>${p.description}</p>

                <div class="quantity-control mb-3">
                    <button onclick="changeQuantity(${p.id}, -1)">-</button>
                    <input type="number" id="qty-${p.id}" value="1">
                    <button onclick="changeQuantity(${p.id}, 1)">+</button>
                </div>

                <button class="btn btn-primary w-100 mb-2" onclick="addToCart(${p.id})">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>

                <button class="btn btn-outline-danger w-100" onclick="toggleWishlist(${p.id})">
                    <i class="bi bi-heart"></i> Wishlist
                </button>
            </div>
        </div>
    `);

    new bootstrap.Modal("#productModal").show();
}

/* ================================
   IMAGE MODAL
================================ */
function openImageModal(src) {
    $("#modalImage").attr("src", src);
    new bootstrap.Modal("#imageModal").show();
}

/* ================================
   QUANTITY MODIFY
================================ */
function changeQuantity(id, delta) {
    const el = document.getElementById(`qty-${id}`);
    let val = parseInt(el.value) + delta;
    if (val < 1) val = 1;
    el.value = val;
}

/* ================================
   CART FUNCTIONS
================================ */
async function fetchCart() {
    const res = await fetch(`${API_BASE}CartItemRoutes.php?user_id=${appState.currentUser.id}`);
    const json = await res.json();
    appState.cart = json.data;
    renderCartPage();
}

function renderCartPage() {
    const el = $("#cartContainer");
    if (!el.length) return;

    if (appState.cart.length === 0) {
        el.html(`<div class='alert alert-info'>Your cart is empty.</div>`);
        updateBadges();
        return;
    }

    let total = 0;

    el.html(`
        <table class="table">
        ${appState.cart.map(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            return `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>$${item.price}</td>
                    <td>$${subtotal}</td>
                </tr>
            `;
        }).join("")}
        </table>

        <h4 class="text-end">Total: $${total.toFixed(2)}</h4>
    `);

    updateBadges();
}

async function addToCart(id) {
    const qty = document.getElementById(`qty-${id}`)?.value ?? 1;

    await fetch(`${API_BASE}CartItemRoutes.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            user_id: appState.currentUser.id,
            product_id: id,
            quantity: parseInt(qty)
        })
    });

    showAlert("Added to cart!");
    fetchCart();
}

/* ================================
   WISHLIST FUNCTIONS
================================ */
function isInWishlist(id) {
    return appState.wishlist.some(w => w.product_id === id || w.id === id);
}

async function fetchWishlist() {
    const res = await fetch(`${API_BASE}WishlistItemRoutes.php?user_id=${appState.currentUser.id}`);
    const json = await res.json();
    appState.wishlist = json.data;
    updateBadges();
}

async function toggleWishlist(id) {
    if (isInWishlist(id)) {
        await fetch(`${API_BASE}WishlistItemRoutes.php?id=${id}`, { method: "DELETE" });
        showAlert("Removed from wishlist", "warning");
    } else {
        await fetch(`${API_BASE}WishlistItemRoutes.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                user_id: appState.currentUser.id,
                product_id: id
            })
        });
        showAlert("Added to wishlist!", "success");
    }

    fetchWishlist();
    renderProductsPage();
}

/* ================================
   PAGE CONTROLLER
================================ */
$(document).ready(() => {
    const hash = location.hash;

    if (hash.includes("products")) fetchProducts();
    if (hash.includes("wishlist")) fetchWishlist();
    if (hash.includes("cart")) fetchCart();

    updateBadges();
});
/* ===================================
   CHECKOUT STEP WIZARD
=================================== */

function showCheckoutStep(step) {
    $(".checkout-step").removeClass("active");
    $("#step" + step).addClass("active");

    $(".step-circle").removeClass("active");
    $(`.step-circle[data-step="${step}"]`).addClass("active");
}

// Next buttons
$(document).on("click", "[data-next]", function () {
    const next = $(this).data("next");
    showCheckoutStep(next);

    if (next === 3) loadReviewOrder();
});

// Back buttons
$(document).on("click", "[data-back]", function () {
    const back = $(this).data("back");
    showCheckoutStep(back);
});

// Load cart review
function loadReviewOrder() {
    const tbody = $("#reviewTbody");
    tbody.html("");

    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    cart.forEach(item => {
        tbody.append(`
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>$${item.price}</td>
                <td>$${(item.price * item.quantity).toFixed(2)}</td>
            </tr>
        `);
    });
}

// Finish order
$("#finishOrder").on("click", () => {
    $("#payMsg").html(`
        <div class="alert alert-success mt-3">
            <i class="bi bi-check-circle"></i> Payment successful! Your order is placed.
        </div>
    `);
});
/* ===================================
   AUTH FORMS (LOGIN & SIGNUP)
   (frontend only for now)
=================================== */

$(document).ready(function () {
  // Handle login submit
  $(document).on("submit", "#loginForm", function (e) {
    e.preventDefault();

    const email = $("#loginEmail").val().trim();
    const password = $("#loginPassword").val().trim();

    if (!email || !password) {
      showAlert("Please enter email and password.", "warning");
      return;
    }

    // Later we'll call backend here – for now just info message
    showAlert("Login will be connected to backend in Milestone 3.", "info");
  });

  // Handle signup submit
  $(document).on("submit", "#signupForm", function (e) {
    e.preventDefault();

    const name = $("#signupName").val().trim();
    const surname = $("#signupSurname").val().trim();
    const email = $("#signupEmail").val().trim();
    const password = $("#signupPassword").val().trim();
    const gender = $("input[name='signupGender']:checked").val();

    if (!name || !surname || !email || !password) {
      showAlert("Please fill in all fields.", "warning");
      return;
    }

    // Later we'll send this to backend and create user
    console.log("Signup data:", { name, surname, email, gender });

    showAlert("Signup will be connected to backend in Milestone 3.", "info");

    // Optionally auto-redirect to login
    setTimeout(() => {
      window.location.hash = "#login";
    }, 800);
  });
});
/* ===================================
   USER PAGE LOGIC (profile & account)
=================================== */

// Prefill fields from appState.currentUser
function prepareUserPage() {
  const u = appState.currentUser || {};

  $("#userName").val(u.name || "");
  $("#userEmail").val(u.email || "");
  $("#userEmailLabel").text(u.email || "guest@example.com");
}

// Handle profile + password form submit
$(document).on("submit", "#userProfileForm", async function (e) {
  e.preventDefault();

  const name = $("#userName").val().trim();
  const email = $("#userEmail").val().trim();
  const currentPass = $("#currentPassword").val();
  const newPass = $("#newPassword").val();
  const confirmPass = $("#confirmPassword").val();

  // Simple front-end validation
  if (!name || !email) {
    showAlert("Please enter your name and email.", "danger");
    return;
  }

  if (newPass || confirmPass || currentPass) {
    if (!currentPass) {
      showAlert("Enter your current password to change it.", "danger");
      return;
    }
    if (newPass.length < 6) {
      showAlert("New password should be at least 6 characters.", "danger");
      return;
    }
    if (newPass !== confirmPass) {
      showAlert("New password and confirmation do not match.", "danger");
      return;
    }
  }

  // TODO: call backend once you create UserRoutes.php
  // Example structure (you can uncomment when backend is ready):
  /*
  try {
    const res = await fetch(`${API_BASE}UserRoutes.php`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id: appState.currentUser.id,
        name,
        email,
        current_password: currentPass,
        new_password: newPass || null
      })
    });
    const json = await res.json();

    if (json.success) {
      appState.currentUser.name = name;
      appState.currentUser.email = email;
      prepareUserPage();
      showAlert("Profile updated successfully.", "success");
    } else {
      showAlert(json.message || "Failed to update profile.", "danger");
    }
  } catch (err) {
    console.error(err);
    showAlert("Server error while updating profile.", "danger");
  }
  */

  // For now (frontend only) just update local state & show success
  appState.currentUser.name = name;
  appState.currentUser.email = email;
  prepareUserPage();
  showAlert("Profile changes saved (frontend only, backend later).", "success");
});

// Logout button
$(document).on("click", "#btnLogout", function () {
  // Clear user (later you can also clear tokens, sessions, etc.)
  appState.currentUser = { id: 0, name: "Guest", email: "" };
  updateBadges();
  showAlert("You have been signed out.", "info");
  window.location.hash = "#login";
});

// Go to login page
$(document).on("click", "#btnGoToLogin", function () {
  window.location.hash = "#login";
});

// Delete account button
$(document).on("click", "#btnDeleteAccount", async function () {
  if (!confirm("Are you sure? This will permanently delete your account later.")) {
    return;
  }

  // TODO: backend call (when you create delete route)
  /*
  try {
    const res = await fetch(`${API_BASE}UserRoutes.php?id=${appState.currentUser.id}`, {
      method: "DELETE"
    });
    const json = await res.json();

    if (json.success) {
      appState.currentUser = { id: 0, name: "Guest", email: "" };
      appState.cart = [];
      appState.wishlist = [];
      updateBadges();
      showAlert("Account deleted successfully.", "success");
      window.location.hash = "#home";
    } else {
      showAlert(json.message || "Failed to delete account.", "danger");
    }
  } catch (err) {
    console.error(err);
    showAlert("Server error while deleting account.", "danger");
  }
  */

  // Frontend-only behaviour for now
  appState.currentUser = { id: 0, name: "Guest", email: "" };
  appState.cart = [];
  appState.wishlist = [];
  updateBadges();
  showAlert("Account deleted (frontend only, connect backend later).", "warning");
  window.location.hash = "#home";
});

// When user page is opened via hash (#user), fill the fields
function handleUserPageHash() {
  if (location.hash === "#user") {
    // small delay so HTML is loaded by spapp before we touch DOM
    setTimeout(prepareUserPage, 80);
  }
}

// run once on load
$(document).ready(function () {
  handleUserPageHash();
  $(window).on("hashchange", handleUserPageHash);
});

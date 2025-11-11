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

/* ===================================
   FISHING PLANET - COMPLETE JAVASCRIPT
   With All Missing Functions
=================================== */

const API_BASE = "http://localhost/WEB-FULLSTACK-Fising.Store/BACKEND/";

/* ================================
   GLOBAL APP STATE
================================ */
let appState = {
    products: [],
    filteredProducts: [],
    cart: [],
    wishlist: [],
    currentUser: null
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
   AUTH HELPER - fetchAuth
================================ */
async function fetchAuth(endpoint, options = {}) {
    const token = localStorage.getItem("user_token");
    
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };

    if (token) {
        headers['Authentication'] = token;
    }

    const response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers: headers
    });

    // If 401, logout user
    if (response.status === 401) {
        logout();
        return null;
    }

    return response;
}

/* ================================
   CHECK IF USER IS LOGGED IN
================================ */
function checkAuth() {
    const token = localStorage.getItem("user_token");
    const user = localStorage.getItem("user");
    
    if (token && user) {
        appState.currentUser = JSON.parse(user);
        updateUIForLoggedInUser();
        return true;
    }
    
    appState.currentUser = null;
    updateUIForLoggedOutUser();
    return false;
}

/* ================================
   UPDATE UI BASED ON AUTH STATE
================================ */
function updateUIForLoggedInUser() {
    $("#loginNav").hide();
    $("#signupNav").hide();
    $("#logoutNav").show();
    $("#cartNav").show();
    $("#wishlistNav").show();
    $("#userInfo").text(`Welcome, ${appState.currentUser.username}!`).show();
    
    // Show admin link if admin
    if (appState.currentUser.role === 'admin') {
        $("#adminNav").show();
    } else {
        $("#adminNav").hide();
    }
}

function updateUIForLoggedOutUser() {
    $("#loginNav").show();
    $("#signupNav").show();
    $("#logoutNav").hide();
    $("#cartNav").hide();
    $("#wishlistNav").hide();
    $("#adminNav").hide();
    $("#userInfo").hide();
}

/* ================================
   LOGOUT
================================ */
function logout() {
    localStorage.removeItem("user_token");
    localStorage.removeItem("user");
    appState.currentUser = null;
    appState.cart = [];
    appState.wishlist = [];
    window.location.hash = "#home";
    showAlert("You have been logged out.", "info");
    updateBadges();
    updateUIForLoggedOutUser();
}

/* ================================
   LOGIN LOGIC
================================ */
$(document).on("submit", "#loginForm", async function (e) {
    e.preventDefault();

    const email = $("#loginEmail").val().trim();
    const password = $("#loginPassword").val().trim();

    try {
        // Updated URL to match Lab structure: API_BASE + "auth/login" 
        const response = await fetch(`${API_BASE}auth/login`, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json" 
            },
            body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Store token as per Lab 10 [cite: 638]
            localStorage.setItem("user_token", result.data.token);
            localStorage.setItem("user", JSON.stringify(result.data.user)); // assuming user object is returned
            
            appState.currentUser = result.data.user;
            
            showAlert("Login successful! Redirecting...", "success");
            setTimeout(() => {
                window.location.hash = "#home";
                location.reload(); 
            }, 1000);
        } else {
            showAlert(result.message || "Login failed", "danger");
        }
    } catch (error) {
        console.error("Login error:", error);
        showAlert("Server error. Please try again later.", "danger");
    }
});

/* ================================
   SIGNUP LOGIC
================================ */
$(document).on("submit", "#signupForm", async function (e) {
    // ... (previous code) ...

    try {
        const response = await fetch(`${API_BASE}auth/register`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(signupData)
        });
        
        const result = await response.json();

        if (response.ok && result.success) {
            // === NEW: AUTO LOGIN ===
            localStorage.setItem("user_token", result.data.token);
            localStorage.setItem("user", JSON.stringify(result.data.user));
            appState.currentUser = result.data.user;

            showAlert("Account created! Logging you in...", "success");
            
            // Redirect directly to Home instead of Login
            setTimeout(() => { 
                window.location.hash = "#home"; 
                location.reload(); 
            }, 1500);
        } else {
            showAlert(result.message || "Registration failed", "danger");
        }
    } catch (error) {
        console.error("Registration error:", error);
        showAlert("Server error", "danger");
    }
});

/* ================================
   FETCH PRODUCTS (PUBLIC)
================================ */
async function fetchProducts() {
    try {
        const res = await fetch(`${API_BASE}products`);
        const json = await res.json();

        if (json.success) {
            appState.products = json.data.map(p => ({
                id: p.product_id,
                name: p.name,
                category: p.category ?? "general",
                price: parseFloat(p.price),
                image: p.image_url,
                description: p.description ?? "No description",
                stock: p.stock_quantity ?? 0
            }));

            appState.filteredProducts = [...appState.products];
            renderProductsPage();
        }
    } catch (err) {
        console.error("Product fetch error:", err);
        showAlert("Cannot load products", "danger");
    }
}

/* ================================
   RENDER PRODUCTS
================================ */
function renderProductsPage() {
    const container = $("#productsContainer");
    if (!container.length) return;

    if (appState.filteredProducts.length === 0) {
        container.html('<div class="col-12 text-center"><p>No products found</p></div>');
        return;
    }

    container.html(
        appState.filteredProducts.map(p => `
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="product-card">
                <div class="product-image-container">
                    <img src="${p.image}" class="product-image" onclick="openImageModal('${p.image}', '${p.name}')" alt="${p.name}">
                    <button class="wishlist-btn ${isInWishlist(p.id) ? "active" : ""}"
                            onclick="toggleWishlist(${p.id})" ${!checkAuth() ? 'disabled' : ''}>
                        <i class="bi bi-heart${isInWishlist(p.id) ? '-fill' : ''}"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="product-category">${p.category}</div>
                    <h5 class="product-title">${p.name}</h5>
                    <div class="product-price">$${p.price.toFixed(2)}</div>
                    <div class="product-stock ${p.stock > 0 ? 'text-success' : 'text-danger'}">
                        ${p.stock > 0 ? `In Stock (${p.stock})` : 'Out of Stock'}
                    </div>
                    ${p.stock > 0 ? `
                    <div class="quantity-control">
                        <button onclick="changeQuantity(${p.id}, -1)">-</button>
                        <input type="number" id="qty-${p.id}" value="1" min="1" max="${p.stock}">
                        <button onclick="changeQuantity(${p.id}, 1)">+</button>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="addToCart(${p.id})">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                    ` : '<div class="alert alert-warning">Unavailable</div>'}
                </div>
            </div>
        </div>
        `).join("")
    );
}

/* ================================
   IMAGE MODAL (MISSING)
================================ */
function openImageModal(imageUrl, productName = "Product Image") {
    const modalHtml = `
        <div class="modal fade" id="imageModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${productName}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageUrl}" class="img-fluid" alt="${productName}">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#imageModal').remove();
    
    // Add and show new modal
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

/* ================================
   SEARCH & FILTER (MISSING)
================================ */
function searchProducts(searchTerm) {
    const term = searchTerm.toLowerCase();
    appState.filteredProducts = appState.products.filter(p => 
        p.name.toLowerCase().includes(term) || 
        p.category.toLowerCase().includes(term)
    );
    renderProductsPage();
}

function filterByCategory(category) {
    if (category === 'all') {
        appState.filteredProducts = [...appState.products];
    } else {
        appState.filteredProducts = appState.products.filter(p => 
            p.category.toLowerCase() === category.toLowerCase()
        );
    }
    renderProductsPage();
}

// Search form handler
$(document).on("submit", "#searchForm", function(e) {
    e.preventDefault();
    const searchTerm = $("#searchInput").val();
    searchProducts(searchTerm);
});

/* ================================
   CART FUNCTIONS
================================ */
async function fetchCart() {
    if (!checkAuth()) {
        appState.cart = [];
        updateBadges();
        return;
    }

    const res = await fetchAuth(`cart`);
    
    if (res && res.ok) {
        const json = await res.json();
        if (json.success && json.data) {
            appState.cart = json.data.items || json.data || [];
            updateBadges();
        }
    }
}

async function addToCart(productId) {
    if (!checkAuth()) {
        showAlert("Please login to add items to cart!", "warning");
        window.location.hash = "#login";
        return;
    }

    const qtyInput = document.getElementById(`qty-${productId}`);
    const qty = qtyInput ? parseInt(qtyInput.value) : 1;

    const res = await fetchAuth(`cart`, {
        method: "POST",
        body: JSON.stringify({
            product_id: productId,
            quantity: qty
        })
    });

    if (res && res.ok) {
        showAlert("Added to cart!", "success");
        await fetchCart();
        renderCartPage();
    } else {
        const error = await res.json();
        showAlert(error.message || "Failed to add to cart", "danger");
    }
}

/* ================================
   RENDER CART PAGE (MISSING)
================================ */
async function renderCartPage() {
    await fetchCart();
    
    const container = $("#cartContainer");
    if (!container.length) return;

    if (appState.cart.length === 0) {
        container.html(`
            <div class="text-center py-5">
                <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
                <h3>Your cart is empty</h3>
                <a href="#products" class="btn btn-primary mt-3">Start Shopping</a>
            </div>
        `);
        return;
    }

    let total = 0;
    const cartHtml = appState.cart.map(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        
        return `
            <div class="cart-item mb-3">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="${item.image_url}" class="img-fluid" alt="${item.name}">
                    </div>
                    <div class="col-md-4">
                        <h5>${item.name}</h5>
                        <p class="text-muted">$${parseFloat(item.price).toFixed(2)} each</p>
                    </div>
                    <div class="col-md-3">
                        <div class="quantity-control">
                            <button onclick="updateCartQuantity(${item.cart_item_id}, ${item.quantity - 1})">-</button>
                            <input type="number" value="${item.quantity}" readonly>
                            <button onclick="updateCartQuantity(${item.cart_item_id}, ${item.quantity + 1})">+</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <strong>$${subtotal.toFixed(2)}</strong>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.cart_item_id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    container.html(`
        ${cartHtml}
        <hr>
        <div class="text-end">
            <h4>Total: $${total.toFixed(2)}</h4>
            <button class="btn btn-success btn-lg mt-3" onclick="proceedToCheckout()">
                <i class="bi bi-credit-card"></i> Proceed to Checkout
            </button>
        </div>
    `);
}

/* ================================
   UPDATE CART QUANTITY (MISSING)
================================ */
async function updateCartQuantity(cartItemId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(cartItemId);
        return;
    }

    const res = await fetchAuth(`cart/${cartItemId}`, {
        method: "PUT",
        body: JSON.stringify({ quantity: newQuantity })
    });

    if (res && res.ok) {
        showAlert("Cart updated!", "success");
        renderCartPage();
    } else {
        showAlert("Failed to update cart", "danger");
    }
}

/* ================================
   REMOVE FROM CART (MISSING)
================================ */
async function removeFromCart(cartItemId) {
    if (!confirm("Remove this item from cart?")) return;

    const res = await fetchAuth(`cart/${cartItemId}`, {
        method: "DELETE"
    });

    if (res && res.ok) {
        showAlert("Item removed from cart", "info");
        renderCartPage();
    } else {
        showAlert("Failed to remove item", "danger");
    }
}

/* ================================
   CHECKOUT (MISSING)
================================ */
function proceedToCheckout() {
    if (appState.cart.length === 0) {
        showAlert("Your cart is empty!", "warning");
        return;
    }
    
    // For now, just show alert - you can implement full checkout later
    showAlert("Checkout feature coming soon!", "info");
    // window.location.hash = "#checkout";
}

/* ================================
   WISHLIST FUNCTIONS
================================ */
function isInWishlist(id) {
    return appState.wishlist.some(w => w.product_id === id);
}

async function fetchWishlist() {
    if (!checkAuth()) {
        appState.wishlist = [];
        updateBadges();
        return;
    }

    const res = await fetchAuth(`wishlist`);
    if (res && res.ok) {
        const json = await res.json();
        appState.wishlist = json.data || [];
        updateBadges();
    }
}

async function toggleWishlist(id) {
    if (!checkAuth()) {
        showAlert("Please login!", "warning");
        window.location.hash = "#login";
        return;
    }

    const wishlistItem = appState.wishlist.find(w => w.product_id === id);

    if (wishlistItem) {
        const res = await fetchAuth(`wishlist/${wishlistItem.wishlist_item_id}`, { 
            method: "DELETE" 
        });
        if (res && res.ok) {
            showAlert("Removed from wishlist", "info");
        }
    } else {
        const res = await fetchAuth(`wishlist`, {
            method: "POST",
            body: JSON.stringify({ product_id: id })
        });
        if (res && res.ok) {
            showAlert("Added to wishlist!", "success");
        }
    }

    await fetchWishlist();
    renderProductsPage();
}

/* ================================
   RENDER WISHLIST PAGE (MISSING)
================================ */
async function renderWishlistPage() {
    await fetchWishlist();
    
    const container = $("#wishlistContainer");
    if (!container.length) return;

    if (appState.wishlist.length === 0) {
        container.html(`
            <div class="text-center py-5">
                <i class="bi bi-heart" style="font-size: 4rem; color: #ccc;"></i>
                <h3>Your wishlist is empty</h3>
                <a href="#products" class="btn btn-primary mt-3">Browse Products</a>
            </div>
        `);
        return;
    }

    const wishlistHtml = appState.wishlist.map(item => `
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="product-card">
                <div class="product-image-container">
                    <img src="${item.image_url}" class="product-image" alt="${item.name}">
                    <button class="wishlist-btn active" onclick="toggleWishlist(${item.product_id})">
                        <i class="bi bi-heart-fill"></i>
                    </button>
                </div>
                <div class="card-body">
                    <h5 class="product-title">${item.name}</h5>
                    <div class="product-price">$${parseFloat(item.price).toFixed(2)}</div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="addToCartFromWishlist(${item.product_id})">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    container.html(`<div class="row">${wishlistHtml}</div>`);
}

async function addToCartFromWishlist(productId) {
    await addToCart(productId);
}

/* ================================
   QUANTITY CONTROLS
================================ */
function changeQuantity(id, delta) {
    const el = document.getElementById(`qty-${id}`);
    if (!el) return;
    
    let val = parseInt(el.value) + delta;
    const max = parseInt(el.getAttribute('max')) || 999;
    
    if (val < 1) val = 1;
    if (val > max) val = max;
    
    el.value = val;
}

/* ================================
   PAGE CONTROLLER & ROUTER
================================ */
function loadPage() {
    const hash = location.hash.substring(1) || "home";
    
    // Hide all sections
    $("section").hide();
    
    // Show requested section
    $(`#${hash}`).show();
    
    // Load data based on page
    switch(hash) {
        case "products":
            fetchProducts();
            break;
        case "cart":
            renderCartPage();
            break;
        case "wishlist":
            renderWishlistPage();
            break;
        case "login":
        case "signup":
            if (checkAuth()) {
                window.location.hash = "#home";
            }
            break;
    }
}

/* ================================
   INITIALIZE APP
================================ */
$(document).ready(() => {
    checkAuth();
    loadPage();
    
    // Handle hash changes
    $(window).on('hashchange', loadPage);
    
    // Update badges on load
    if (checkAuth()) {
        fetchCart();
        fetchWishlist();
    }
});

/* ================================
   ADMIN FUNCTIONS (IF NEEDED)
================================ */
async function deleteProduct(productId) {
    if (!confirm("Delete this product?")) return;
    
    const res = await fetchAuth(`products/${productId}`, {
        method: "DELETE"
    });
    
    if (res && res.ok) {
        showAlert("Product deleted!", "success");
        fetchProducts();
    } else {
        showAlert("Failed to delete product", "danger");
    }
}

async function deleteUser(userId) {
    if (!confirm("Delete this user?")) return;
    
    const res = await fetchAuth(`users/${userId}`, {
        method: "DELETE"
    });
    
    if (res && res.ok) {
        showAlert("User deleted!", "success");
        // Refresh user list
    } else {
        showAlert("Failed to delete user", "danger");
    }
}
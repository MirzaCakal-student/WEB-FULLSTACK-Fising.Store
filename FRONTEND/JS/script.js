/* ===================================
   FISHING PLANET - MAIN JAVASCRIPT
   =================================== */

// ========================================
// APPLICATION STATE
// ========================================
const appState = {
    products: [
        { id: 1, name: "Carbon Fiber Rod", category: "Rods", price: 89.99, quantity: 15, image: "https://images.unsplash.com/photo-1534943441045-ef155a045dcd?w=400&h=300&fit=crop", description: "High-quality carbon fiber fishing rod for professional anglers." },
        { id: 2, name: "Spinning Reel Pro", category: "Reels", price: 129.99, quantity: 20, image: "https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400&h=300&fit=crop", description: "Professional spinning reel with smooth drag system." },
        { id: 3, name: "Worm Bait Set", category: "Baits", price: 19.99, quantity: 50, image: "https://images.unsplash.com/photo-1506277886164-e25aa3a4c9b0?w=400&h=300&fit=crop", description: "Set of realistic worm baits for freshwater fishing." },
        { id: 4, name: "Fishing Net XL", category: "Accessories", price: 49.99, quantity: 25, image: "https://images.unsplash.com/photo-1604764746323-49bcb0b2b1e2?w=400&h=300&fit=crop", description: "Durable large fishing net for big catches." },
        { id: 5, name: "Tackle Box Pro", category: "Accessories", price: 39.99, quantity: 40, image: "https://images.unsplash.com/photo-1603570411731-1d9d92e7f1d0?w=400&h=300&fit=crop", description: "Multi-compartment tackle box for organized fishing gear." }
    ],
    cart: JSON.parse(localStorage.getItem("cart")) || [],
    wishlist: JSON.parse(localStorage.getItem("wishlist")) || [],
    users: JSON.parse(localStorage.getItem("users")) || [
        { username: "admin", password: "admin123", isAdmin: true }
    ],
    currentUser: JSON.parse(localStorage.getItem("currentUser")) || null
};

// ========================================
// UTILITIES
// ========================================
function saveState() {
    localStorage.setItem("cart", JSON.stringify(appState.cart));
    localStorage.setItem("wishlist", JSON.stringify(appState.wishlist));
    localStorage.setItem("users", JSON.stringify(appState.users));
    localStorage.setItem("currentUser", JSON.stringify(appState.currentUser));
}

function showAlert(message, type = "success") {
    const alert = `<div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    $("#alert-container").html(alert);
    setTimeout(() => $(".alert").alert("close"), 3000);
}

// ========================================
// PRODUCT RENDERING
// ========================================
function renderProductsGrid() {
    const container = document.getElementById("productsContainer");
    if (!container) return;

    const searchTerm = $("#searchInput").val()?.toLowerCase() || "";
    const category = $("#categoryFilter").val();

    const filtered = appState.products.filter(p => {
        const matchSearch = p.name.toLowerCase().includes(searchTerm);
        const matchCategory = !category || p.category === category;
        return matchSearch && matchCategory;
    });

    container.innerHTML = filtered.map(p => `
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 product-card">
                <img src="${p.image}" class="card-img-top product-image" alt="${p.name}" onclick="openImageModal('${p.image}')">
                <div class="card-body">
                    <h5 class="card-title fw-semibold">${p.name}</h5>
                    <p class="fw-bold text-success">$${p.price.toFixed(2)}</p>

                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <button class="btn btn-sm btn-outline-success" onclick="changeProductQuantity(${p.id}, -1)">-</button>
                        <input type="number" id="qty-${p.id}" class="form-control form-control-sm mx-2 text-center" style="width: 60px" value="1" min="1" max="${p.quantity}">
                        <button class="btn btn-sm btn-outline-success" onclick="changeProductQuantity(${p.id}, 1)">+</button>
                    </div>

                    <button class="btn btn-success btn-sm me-2" onclick="addToCart(${p.id})">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline-danger btn-sm me-2" onclick="addToWishlist(${p.id})">
                        <i class="bi bi-heart"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="viewSingleProduct(${p.id})">
                        <i class="bi bi-eye"></i> View
                    </button>
                </div>
            </div>
        </div>
    `).join("");
}

// ========================================
// HELPERS FOR PRODUCTS PAGE
// ========================================

function filterProducts() {
    renderProductsGrid();
}

function changeProductQuantity(productId, delta) {
    const input = document.getElementById(`qty-${productId}`);
    if (!input) return;
    let value = parseInt(input.value);
    const product = appState.products.find(p => p.id === productId);
    value = Math.min(Math.max(value + delta, 1), product.quantity);
    input.value = value;
}

function viewSingleProduct(productId) {
    const product = appState.products.find(p => p.id === productId);
    if (!product) return;

    let html = `
    <div class="card p-4">
        <div class="row">
            <div class="col-md-6 text-center">
                <img src="${product.image}" class="img-fluid rounded shadow-sm mb-3" style="cursor:pointer;" onclick="openImageModal('${product.image}')">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold text-success">${product.name}</h2>
                <h4 class="text-primary mb-3">$${product.price.toFixed(2)}</h4>
                <p>${product.description}</p>
                <p><strong>Available:</strong> ${product.quantity}</p>
                <button class="btn btn-success me-2" onclick="addToCart(${product.id})"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                <button class="btn btn-outline-danger" onclick="addToWishlist(${product.id})"><i class="bi bi-heart"></i> Wishlist</button>
            </div>
        </div>
    </div>
    `;
    $("#single-product").html(html);
    window.location.hash = "#single_product";
}

function openImageModal(imageSrc) {
    $("#modalImage").attr("src", imageSrc);
    new bootstrap.Modal(document.getElementById("imageModal")).show();
}


// ========================================
// CART & WISHLIST
// ========================================
function updateCartBadge() {
    $("#cart-count").text(appState.cart.length);
}
function updateWishlistBadge() {
    $("#wishlist-count").text(appState.wishlist.length);
}

function addToCart(id) {
    const product = appState.products.find(p => p.id == id);
    if (!product) return;
    const item = appState.cart.find(i => i.id == id);
    if (item) item.quantity++;
    else appState.cart.push({ ...product, quantity: 1 });
    saveState();
    updateCartBadge();
    showAlert(`${product.name} added to cart.`);
}

function addToWishlist(id) {
    const product = appState.products.find(p => p.id == id);
    if (!product) return;
    if (!appState.wishlist.some(i => i.id == id)) {
        appState.wishlist.push(product);
        saveState();
        updateWishlistBadge();
        showAlert(`${product.name} added to wishlist.`);
    }
}

// ========================================
// CHECKOUT LOGIC
// ========================================
function validateCheckoutStep(step) {
    let valid = true;
    $(`#checkout-step-${step} input[required]`).each(function () {
        if (!$(this).val()) {
            $(this).addClass("is-invalid");
            valid = false;
        } else $(this).removeClass("is-invalid");
    });
    return valid;
}

function handlePayment() {
    const method = $("input[name='paymentMethod']:checked").val();
    if (method === "card") {
        if (validateCheckoutStep(4)) {
            showAlert("Payment successful with card!", "success");
            appState.cart = [];
            saveState();
            updateCartBadge();
        } else {
            showAlert("Please fill all card details.", "danger");
        }
    } else if (method === "paypal") {
        showAlert("Purchase completed with PayPal!", "success");
        appState.cart = [];
        saveState();
        updateCartBadge();
    }
}

// ========================================
// LOGIN / SIGNUP
// ========================================
function handleLogin(username, password) {
    const user = appState.users.find(u => u.username === username && u.password === password);
    if (user) {
        appState.currentUser = user;
        saveState();
        showAlert(`Welcome, ${user.username}!`);
        window.location.hash = user.isAdmin ? "#admin" : "#home";
    } else {
        showAlert("Invalid credentials", "danger");
    }
}

function handleSignup(username, password) {
    if (appState.users.some(u => u.username === username)) {
        showAlert("Username already exists", "danger");
        return;
    }
    appState.users.push({ username, password, isAdmin: false });
    saveState();
    showAlert("Account created successfully!");
    window.location.hash = "#login";
}

// ========================================
// ADMIN PANEL
// ========================================
function renderAdminProducts() {
    const container = $("#admin-products");
    container.html("");
    appState.products.forEach(p => {
        container.append(`
            <tr>
                <td>${p.id}</td>
                <td><input type="text" class="form-control name" value="${p.name}" data-id="${p.id}"></td>
                <td><input type="number" class="form-control price" value="${p.price}" data-id="${p.id}"></td>
                <td><input type="number" class="form-control quantity" value="${p.quantity}" data-id="${p.id}"></td>
            </tr>
        `);
    });

    $("#save-admin").off("click").on("click", function () {
        $("#admin-products tr").each(function () {
            const id = $(this).find(".name").data("id");
            const product = appState.products.find(p => p.id == id);
            product.name = $(this).find(".name").val();
            product.price = parseFloat($(this).find(".price").val());
            product.quantity = parseInt($(this).find(".quantity").val());
        });
        saveState();
        showAlert("Products updated successfully!");
    });
}

// ========================================
// SPA INITIALIZATION (SPAPP FIXED VERSION)
// ========================================
$(document).ready(function () {
    var app = $.spapp({
        defaultView: "home",
        templateDir: "HTML/"
    });

    // Define SPA routes (no absolute paths)
    app.route({ view: "home", load: "home.html" });
    app.route({ view: "products", load: "products.html" });
    app.route({ view: "single_product", load: "single_product.html" });
    app.route({ view: "aboutus", load: "aboutus.html" });
    app.route({ view: "cart", load: "cart.html" });
    app.route({ view: "wishlist", load: "wishlist.html" });
    app.route({ view: "checkout", load: "checkout.html" });
    app.route({ view: "login", load: "login.html" });
    app.route({ view: "signup", load: "signup.html" });
    app.route({ view: "user", load: "user.html" });
    app.route({ view: "admin", load: "admin.html" });

    // Run SPA
    app.run();

    // Global event listeners
    $(document)
        .on("click", ".add-to-cart", function () {
            addToCart($(this).data("id"));
        })
        .on("click", ".add-to-wishlist", function () {
            addToWishlist($(this).data("id"));
        })
        .on("click", ".product-img", function () {
            const id = $(this).data("id");
            window.location.hash = `#single_product?id=${id}`;
        });


    updateCartBadge();
    updateWishlistBadge();

    $(window).on("hashchange", function() {
    if (location.hash === "#products") {
        setTimeout(renderProductsGrid, 150); // delay to ensure DOM is ready
    }
});


    $(window).on("hashchange", function () {
        const current = location.hash.replace("#", "");
        $("main#spapp > section").hide();
        $(`#${current}`).show();
    });


    $(window).trigger("hashchange");
});

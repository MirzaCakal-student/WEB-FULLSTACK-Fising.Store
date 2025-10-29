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
// CART & WISHLIST STATE
// ========================================
let cart = JSON.parse(localStorage.getItem("cart")) || [];
let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

// Save to localStorage
function saveCart() { localStorage.setItem("cart", JSON.stringify(cart)); }
function saveWishlist() { localStorage.setItem("wishlist", JSON.stringify(wishlist)); }

// Update badges (if you have icons in navbar)
function updateBadges() {
  const c = document.getElementById("cart-count");
  const w = document.getElementById("wishlist-count");
  if (c) c.textContent = cart.length;
  if (w) w.textContent = wishlist.length;
}
updateBadges();

// ========================================
// CART FUNCTIONS
// ========================================
function addToCart(productId) {
  const p = appState.products.find(x => x.id === productId);
  const qtyInput = document.getElementById(`qty-${productId}`);
  const qty = qtyInput ? parseInt(qtyInput.value) : 1;

  const existing = cart.find(x => x.id === p.id);
  if (existing) {
    existing.quantity += qty;
  } else {
    cart.push({ ...p, quantity: qty });
  }

  saveCart();
  updateBadges();
  alert(`${p.name} added to your cart.`);
}

function removeFromCart(productId) {
  cart = cart.filter(p => p.id !== productId);
  saveCart();
  renderCart();
  updateBadges();
}

function changeCartQuantity(productId, delta) {
  const item = cart.find(p => p.id === productId);
  if (!item) return;
  item.quantity += delta;
  if (item.quantity < 1) item.quantity = 1;
  saveCart();
  renderCart();
}

// ========================================
// WISHLIST FUNCTIONS
// ========================================
function addToWishlist(productId) {
  const p = appState.products.find(x => x.id === productId);
  if (wishlist.find(x => x.id === productId)) {
    alert("This product is already in your wishlist.");
    return;
  }
  wishlist.push(p);
  saveWishlist();
  updateBadges();
  alert(`${p.name} added to your wishlist.`);
}

function removeFromWishlist(productId) {
  wishlist = wishlist.filter(p => p.id !== productId);
  saveWishlist();
  renderWishlist();
  updateBadges();
}

// ========================================
// CART PAGE RENDERING
// ========================================
function renderCart() {
  const container = document.getElementById("cartContainer");
  if (!container) return;

  if (cart.length === 0) {
    container.innerHTML = `<div class="alert alert-info">Your cart is empty.</div>`;
    return;
  }

  let total = 0;
  container.innerHTML = `
    <div class="card shadow-sm p-3">
      <table class="table align-middle">
        <thead class="table-primary">
          <tr>
            <th>Product</th>
            <th class="text-center">Qty</th>
            <th>Price</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          ${cart.map(p => {
            const sub = p.price * p.quantity;
            total += sub;
            return `
              <tr>
                <td class="fw-semibold">${p.name}</td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-primary me-1" onclick="changeCartQuantity(${p.id}, -1)">-</button>
                  ${p.quantity}
                  <button class="btn btn-sm btn-outline-primary ms-1" onclick="changeCartQuantity(${p.id}, 1)">+</button>
                </td>
                <td>$${p.price.toFixed(2)}</td>
                <td class="fw-bold text-primary">$${sub.toFixed(2)}</td>
                <td>
                  <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${p.id})">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            `;
          }).join("")}
        </tbody>
      </table>

      <div class="text-end mt-3">
        <h4 class="text-primary fw-bold">Total: $${total.toFixed(2)}</h4>
        <button class="btn btn-success mt-3" onclick="showPage('checkout')">
          <i class="bi bi-check-circle"></i> Proceed to Checkout
        </button>
      </div>
    </div>
  `;
}

// ========================================
// WISHLIST PAGE RENDERING
// ========================================
function renderWishlist() {
  const container = document.getElementById("wishlistContainer");
  if (!container) return;

  if (wishlist.length === 0) {
    container.innerHTML = `<div class="alert alert-info">No liked products yet.</div>`;
    return;
  }

  container.innerHTML = `
    <div class="row">
      ${wishlist.map(p => `
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm border-0">
            <img src="${p.image}" class="card-img-top" alt="${p.name}" style="height: 220px; object-fit: cover;">
            <div class="card-body text-center">
              <h5 class="card-title fw-semibold">${p.name}</h5>
              <p class="text-primary fw-bold">$${p.price.toFixed(2)}</p>
              <button class="btn btn-outline-danger btn-sm me-2" onclick="removeFromWishlist(${p.id})">
                <i class="bi bi-heartbreak"></i> Unlike
              </button>
              <button class="btn btn-outline-secondary btn-sm" onclick="viewSingleProduct(${p.id})">
                <i class="bi bi-eye"></i> Inspect
              </button>
            </div>
          </div>
        </div>
      `).join("")}
    </div>
  `;
}


// ========================================
// CHECKOUT LOGIC
// ========================================
let chStep = 1;

function initCheckout() {
  chStep = 1;
  showCheckoutStep(chStep);
  buildReviewTable();
}

// Show correct step
function showCheckoutStep(n) {
  chStep = n;

  // Hide all steps
  document.querySelectorAll('#checkout .step').forEach(s => s.classList.remove('active'));
  const active = document.querySelector(`#checkout #step${n}`);
  if (active) active.classList.add('active');

  // Update tracker circles (if present)
  const circles = document.querySelectorAll('#checkout .d-flex > .rounded-circle');
  if (circles.length) {
    circles.forEach((c, i) => {
      c.classList.remove('bg-primary', 'text-white');
      c.classList.add('bg-light', 'text-muted');
      if (i < n) {
        c.classList.remove('bg-light', 'text-muted');
        c.classList.add('bg-primary', 'text-white');
      }
    });
  }
}

// Build order review table dynamically
function buildReviewTable() {
  const tbody = document.getElementById('reviewTbody');
  if (!tbody) return;

  const items = window.cart && Array.isArray(window.cart)
    ? window.cart
    : JSON.parse(localStorage.getItem('cart') || '[]');

  let total = 0;
  const rows = items.map(it => {
    const price = Number(it.price) || 0;
    const qty = Number(it.quantity) || 1;
    const subtotal = price * qty;
    total += subtotal;
    return `
      <tr>
        <td>${it.name ?? '-'}</td>
        <td>${qty}</td>
        <td>${price.toFixed(2)}</td>
        <td>${subtotal.toFixed(2)}</td>
      </tr>`;
  });

  tbody.innerHTML = rows.length
    ? rows.join('') + `<tr class="table-light">
        <td colspan="3" class="text-end"><strong>Total</strong></td>
        <td><strong>${total.toFixed(2)}</strong></td>
      </tr>`
    : `<tr><td colspan="4" class="text-center text-muted">Your cart is empty.</td></tr>`;
}

// ========== Step Navigation ==========
document.addEventListener('click', (e) => {
  // Step 1 → 2
  if (e.target.id === 'toStep2') {
    const name = document.getElementById('chName')?.value.trim();
    const email = document.getElementById('chEmail')?.value.trim();
    const phone = document.getElementById('chPhone')?.value.trim();
    if (!name || !email || !phone) {
      alert('Please fill in name, email, and phone.');
      return;
    }
    showCheckoutStep(2);
  }

  // Step 2 → 3
  if (e.target.id === 'toStep3') {
    const addr = document.getElementById('chAddr')?.value.trim();
    const city = document.getElementById('chCity')?.value.trim();
    const zip = document.getElementById('chZip')?.value.trim();
    const country = document.getElementById('chCountry')?.value.trim();
    if (!addr || !city || !zip || !country) {
      alert('Please complete the shipping form.');
      return;
    }
    showCheckoutStep(3);
    buildReviewTable();
  }

  // Step 3 → 4
  if (e.target.id === 'toStep4') {
    showCheckoutStep(4);
  }

  // Back buttons
  if (e.target.classList.contains('backStep')) {
    const target = Number(e.target.dataset.back || 1);
    showCheckoutStep(target);
  }

  // Finish order / Payment
  if (e.target.id === 'finishOrder') {
    const method = document.querySelector('input[name="payMethod"]:checked')?.value || 'card';
    if (method === 'card') {
      const n = document.getElementById('cardNumber')?.value.replace(/\s+/g, '');
      const nm = document.getElementById('cardName')?.value.trim();
      const ex = document.getElementById('cardExp')?.value.trim();
      const cv = document.getElementById('cardCvc')?.value.trim();
      if (!n || n.length < 15 || !nm || !/^\d{2}\/\d{2}$/.test(ex) || !cv || cv.length < 3) {
        alert('Please enter valid card details.');
        return;
      }
    }
    document.getElementById('payMsg').innerHTML =
      `<div class="alert alert-success mt-3">✅ Payment successful! Thank you for your order.</div>`;
    cart = [];
    localStorage.removeItem('cart');
    updateBadges();
    setTimeout(() => showPage('home'), 2000);
  }
});

// Toggle card vs PayPal fields
document.addEventListener('change', (e) => {
  if (e.target.name === 'payMethod') {
    const cardFields = document.getElementById('cardFields');
    if (!cardFields) return;
    cardFields.style.display = (e.target.value === 'card') ? '' : 'none';
  }
});

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

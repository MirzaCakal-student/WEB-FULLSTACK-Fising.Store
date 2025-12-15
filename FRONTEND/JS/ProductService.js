/*finished*/
/* ===================================
   PRODUCT SERVICE
=================================== */

const ProductService = {
  
  allProducts: [],
  filteredProducts: [],

  /**
   * Initialize products view
   */
  init: function() {
    console.log('âœ… ProductService initialized');
    this.loadProducts();
    this.setupEventHandlers();
  },

  /**
   * Setup search and filter event handlers
   */
  setupEventHandlers: function() {
    // Search
    $(document).off('input', '#prod-search').on('input', '#prod-search', function() {
      ProductService.filterProducts();
    });

    // Category filter
    $(document).off('change', '#prod-category').on('change', '#prod-category', function() {
      ProductService.filterProducts();
    });

    // Sort
    $(document).off('change', '#prod-sort').on('change', '#prod-sort', function() {
      ProductService.sortProducts();
    });
  },

  /**
   * Load all products from backend
   */
  loadProducts: function() {
    RestClient.get('products', function(response) {
      if (response.success && response.data) {
        ProductService.allProducts = response.data;
        ProductService.filteredProducts = [...response.data];
        ProductService.renderProducts();
      }
    }, function(xhr) {
      console.error('Failed to load products:', xhr);
      $('#productsContainer').html(
        '<div class="col-12"><div class="alert alert-danger">Failed to load products. Please try again.</div></div>'
      );
    });
  },

  /**
   * Filter products based on search and category
   */
  filterProducts: function() {
    const searchTerm = ($('#prod-search').val() || '').toLowerCase();
    const category = $('#prod-category').val();

    ProductService.filteredProducts = ProductService.allProducts.filter(product => {
      const matchesSearch = !searchTerm || 
        product.name.toLowerCase().includes(searchTerm) ||
        product.category.toLowerCase().includes(searchTerm);
      
      const matchesCategory = !category || product.category === category;

      return matchesSearch && matchesCategory;
    });

    ProductService.sortProducts();
  },

  /**
   * Sort filtered products
   */
  sortProducts: function() {
    const sortBy = $('#prod-sort').val() || 'name-asc';

    ProductService.filteredProducts.sort((a, b) => {
      switch(sortBy) {
        case 'name-asc':
          return a.name.localeCompare(b.name);
        case 'name-desc':
          return b.name.localeCompare(a.name);
        case 'price-asc':
          return parseFloat(a.price) - parseFloat(b.price);
        case 'price-desc':
          return parseFloat(b.price) - parseFloat(a.price);
        default:
          return 0;
      }
    });

    ProductService.renderProducts();
  },

  /**
   * Render products to page
   */
  renderProducts: function() {
    const container = $('#productsContainer');
    
    if (!container.length) {
      console.warn('Products container not found');
      return;
    }

    // Update count
    $('#prod-count').text(`Showing ${ProductService.filteredProducts.length} products`);

    if (ProductService.filteredProducts.length === 0) {
      $('#prod-empty').removeClass('d-none');
      container.empty();
      return;
    }

    $('#prod-empty').addClass('d-none');

    const html = ProductService.filteredProducts.map(product => {
      const imageUrl = Utils.getImageUrl(product.image_url);
      const placeholderImage = Utils.getPlaceholderImage();

      return `
      <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card product-card h-100">
          <div class="product-image-wrap">
            <img src="${imageUrl}"
                 class="product-image"
                 alt="${product.name}"
                 onerror="this.src='${placeholderImage}'"
                 onclick="ProductService.openImageModal('${imageUrl}', '${product.name}')">
            
            ${AuthService.isLoggedIn() ? `
            <button class="wishlist-btn ${WishlistService.isInWishlist(product.product_id) ? 'active' : ''}"
                    onclick="WishlistService.toggle(${product.product_id})">
              <i class="bi bi-heart${WishlistService.isInWishlist(product.product_id) ? '-fill' : ''}"></i>
            </button>
            ` : ''}
            
            ${product.stock_quantity > 0 ? `<span class="product-badge">In Stock</span>` : ''}
          </div>
          
          <div class="card-body d-flex flex-column">
            <small class="product-meta">${product.category}</small>
            <h5 class="product-title">${product.name}</h5>
            <p class="product-price">${parseFloat(product.price).toFixed(2)}</p>
            
            <p class="product-stock ${product.stock_quantity > 0 ? 'stock-available' : 'stock-out'}">
              ${product.stock_quantity > 0 ? `${product.stock_quantity} available` : 'Out of stock'}
            </p>
            
            <div class="mt-auto">
              ${product.stock_quantity > 0 ? `
                <div class="qty-ctrl mb-2">
                  <button onclick="ProductService.changeQty(${product.product_id}, -1)">-</button>
                  <input type="number" id="qty-${product.product_id}" value="1" min="1" max="${product.stock_quantity}" readonly>
                  <button onclick="ProductService.changeQty(${product.product_id}, 1)">+</button>
                </div>
                <button class="btn btn-success w-100" onclick="ProductService.addToCart(${product.product_id})">
                  <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
              ` : `
                <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
              `}
            </div>
          </div>
        </div>
      </div>
    `;
    }).join('');

    container.html(html);
  },

  /**
   * Change quantity input
   */
  changeQty: function(productId, delta) {
    const input = $(`#qty-${productId}`);
    if (!input.length) return;

    const current = parseInt(input.val()) || 1;
    const max = parseInt(input.attr('max')) || 999;
    let newVal = current + delta;

    if (newVal < 1) newVal = 1;
    if (newVal > max) newVal = max;

    input.val(newVal);
  },

  /**
   * Add product to cart
   */
  addToCart: function(productId) {
    if (!AuthService.isLoggedIn()) {
      Utils.showToast('Please login to add items to cart', 'warning');
      window.location.hash = "#login";
      return;
    }

    const qty = parseInt($(`#qty-${productId}`).val()) || 1;
    
    if (typeof CartService !== 'undefined') {
      CartService.addItem(productId, qty);
    } else {
      Utils.showToast('Cart service not available', 'error');
    }
  },

  /**
   * Open image modal
   */
  openImageModal: function(imageUrl, productName) {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    $('#modalImage').attr('src', imageUrl).attr('alt', productName);
    modal.show();
  }
};
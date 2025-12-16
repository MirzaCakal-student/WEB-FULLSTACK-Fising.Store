/*finished*/
/* ===================================
   WISHLIST SERVICE
=================================== */

const WishlistService = {
  
  wishlistItems: [],

  /**
   * Initialize wishlist
   */
  init: function() {
    console.log('âœ… WishlistService initialized');
    this.loadWishlist();
  },

  /**
   * Load wishlist count only (for badge)
   */
  loadWishlistCount: function() {
    if (!AuthService.isLoggedIn()) {
      $('#wishlist-count').text(0);
      return;
    }

    RestClient.get('wishlist-items', function(response) {
      if (response.success && response.data) {
        const count = response.data.length || 0;
        $('#wishlist-count').text(count);
      }
    });
  },

  /**
   * Load full wishlist
   */
  loadWishlist: function() {
    if (!AuthService.isLoggedIn()) {
      this.renderEmptyWishlist();
      return;
    }

    Utils.blockUI('Loading wishlist...');

    RestClient.get('wishlist-items', function(response) {
      Utils.unblockUI();
      
      if (response.success && response.data) {
        WishlistService.wishlistItems = response.data;
        WishlistService.renderWishlist();
        $('#wishlist-count').text(response.data.length);
      }
    }, function(xhr) {
      Utils.unblockUI();
      WishlistService.renderEmptyWishlist();
    });
  },

  /**
   * Check if product is in wishlist
   */
  isInWishlist: function(productId) {
    return this.wishlistItems.some(item => item.product_id == productId);
  },

  /**
   * Toggle wishlist (add/remove)
   */
  toggle: function(productId) {
    if (!AuthService.isLoggedIn()) {
      Utils.showToast('Please login first', 'warning');
      window.location.hash = "#login";
      return;
    }

    const existing = this.wishlistItems.find(item => item.product_id == productId);

    if (existing) {
      // Remove from wishlist
      this.removeItem(existing.wishlist_item_id);
    } else {
      // Add to wishlist
      this.addItem(productId);
    }
  },

  /**
   * Add item to wishlist
   */
  addItem: function(productId) {
    Utils.blockUI('Adding to wishlist...');

    RestClient.post('wishlist-items', {
      product_id: productId
    }, function(response) {
      Utils.unblockUI();
      
      if (response.success) {
        Utils.showToast('Added to wishlist!', 'success');
        WishlistService.loadWishlist();
        
        // Refresh products view if visible
        if (typeof ProductService !== 'undefined' && $('#productsContainer').is(':visible')) {
          ProductService.renderProducts();
        }
      }
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast(xhr.responseJSON?.message || 'Failed to add to wishlist', 'error');
    });
  },

  /**
   * Remove item from wishlist
   */
  removeItem: function(wishlistItemId) {
    Utils.blockUI('Removing...');

    RestClient.delete(`wishlist-items/${wishlistItemId}`, null, function(response) {
      Utils.unblockUI();
      Utils.showToast('Removed from wishlist', 'info');
      WishlistService.loadWishlist();
      
      // Refresh products view if visible
      if (typeof ProductService !== 'undefined' && $('#productsContainer').is(':visible')) {
        ProductService.renderProducts();
      }
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast('Failed to remove from wishlist', 'error');
    });
  },

  /**
   * Render wishlist items
   */
  renderWishlist: function() {
    const container = $('#wishlistGrid');
    const emptyDiv = $('#wishlistEmpty');

    if (!AuthService.isLoggedIn()) {
      this.renderEmptyWishlist();
      return;
    }

    if (this.wishlistItems.length === 0) {
      if (emptyDiv.length) emptyDiv.show();
      if (container.length) container.empty();
      return;
    }

    if (emptyDiv.length) emptyDiv.hide();

    const html = this.wishlistItems.map(item => {
      const imageUrl = Utils.getImageUrl(item.image_url);
      const placeholderImage = Utils.getPlaceholderImage();

      return `
      <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card wishlist-card h-100">
          <img src="${imageUrl}"
               class="card-img-top"
               alt="${item.name}"
               onerror="this.src='${placeholderImage}'"
               style="height: 200px; object-fit: cover;">

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">${item.name}</h5>
            <p class="card-text fs-4 fw-bold text-success">${parseFloat(item.price).toFixed(2)}</p>

            <div class="mt-auto d-flex gap-2">
              <button class="btn btn-success flex-grow-1" onclick="WishlistService.moveToCart(${item.product_id}, ${item.wishlist_item_id})">
                <i class="bi bi-cart-plus"></i> Add to Cart
              </button>
              <button class="btn btn-danger" onclick="WishlistService.removeItem(${item.wishlist_item_id})">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    }).join('');

    if (container.length) {
      container.html(html);
    }
  },

  /**
   * Move item from wishlist to cart
   */
  moveToCart: function(productId, wishlistItemId) {
    if (typeof CartService !== 'undefined') {
      CartService.addItem(productId, 1);
      // Optionally remove from wishlist
      // this.removeItem(wishlistItemId);
    }
  },

  /**
   * Render empty wishlist message
   */
  renderEmptyWishlist: function() {
    const container = $('#wishlistGrid');
    const emptyDiv = $('#wishlistEmpty');
    
    if (container.length) container.empty();
    if (emptyDiv.length) emptyDiv.show();
  }
};
/* ===================================
   CART SERVICE
=================================== */

const CartService = {
  
  cartItems: [],

  /**
   * Initialize cart
   */
  init: function() {
    console.log('âœ… CartService initialized');
    this.loadCart();
  },

  /**
   * Load cart count only (for badge)
   */
  loadCartCount: function() {
    if (!AuthService.isLoggedIn()) {
      Utils.updateBadges(0, null);
      return;
    }

    RestClient.get('cart', function(response) {
      if (response.success && response.data) {
        const count = response.data.length || 0;
        $('#cart-count').text(count);
      }
    });
  },

  /**
   * Load full cart
   */
  loadCart: function() {
    if (!AuthService.isLoggedIn()) {
      this.renderEmptyCart();
      return;
    }

    Utils.blockUI('Loading cart...');

    RestClient.get('cart', function(response) {
      Utils.unblockUI();
      
      if (response.success && response.data) {
        CartService.cartItems = response.data;
        CartService.renderCart();
        $('#cart-count').text(response.data.length);
      }
    }, function(xhr) {
      Utils.unblockUI();
      CartService.renderEmptyCart();
    });
  },

  /**
   * Add item to cart
   */
  addItem: function(productId, quantity = 1) {
    if (!AuthService.isLoggedIn()) {
      Utils.showToast('Please login first', 'warning');
      window.location.hash = "#login";
      return;
    }

    Utils.blockUI('Adding to cart...');

    RestClient.post('cart', {
      product_id: productId,
      quantity: quantity
    }, function(response) {
      Utils.unblockUI();
      
      if (response.success) {
        Utils.showToast('Added to cart!', 'success');
        CartService.loadCartCount();
      }
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast(xhr.responseJSON?.message || 'Failed to add to cart', 'error');
    });
  },

  /**
   * Update cart item quantity
   */
  updateQuantity: function(cartItemId, newQuantity) {
    if (newQuantity < 1) {
      this.removeItem(cartItemId);
      return;
    }

    Utils.blockUI('Updating...');

    RestClient.put(`cart/${cartItemId}`, {
      quantity: newQuantity
    }, function(response) {
      Utils.unblockUI();
      CartService.loadCart();
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast('Failed to update quantity', 'error');
    });
  },

  /**
   * Remove item from cart
   */
  removeItem: function(cartItemId) {
    if (!confirm('Remove this item from cart?')) return;

    Utils.blockUI('Removing...');

    RestClient.delete(`cart/${cartItemId}`, null, function(response) {
      Utils.unblockUI();
      Utils.showToast('Item removed', 'info');
      CartService.loadCart();
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast('Failed to remove item', 'error');
    });
  },

  /**
   * Clear entire cart
   */
  clearCart: function() {
    if (!confirm('Clear entire cart?')) return;

    Utils.blockUI('Clearing cart...');

    RestClient.delete('cart', null, function(response) {
      Utils.unblockUI();
      Utils.showToast('Cart cleared', 'info');
      CartService.loadCart();
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast('Failed to clear cart', 'error');
    });
  },

  /**
   * Render cart items
   */
  renderCart: function() {
    const container = $('#cartContent');
    const emptyDiv = $('#cartEmpty');
    const tbody = $('#cartBody');

    if (!AuthService.isLoggedIn()) {
      this.renderEmptyCart();
      return;
    }

    if (this.cartItems.length === 0) {
      if (emptyDiv.length) emptyDiv.show();
      if (container.length) container.hide();
      return;
    }

    if (emptyDiv.length) emptyDiv.hide();
    if (container.length) container.show();

    let subtotal = 0;
    const html = this.cartItems.map(item => {
      const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
      subtotal += itemTotal;

      const imageUrl = Utils.getImageUrl(item.image_url);
      const placeholderImage = Utils.getPlaceholderImage();

      return `
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <img src="${imageUrl}"
                   alt="${item.name}"
                   onerror="this.src='${placeholderImage}'"
                   style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
              <div>
                <h6 class="mb-0">${item.name}</h6>
                <small class="text-muted">â‚¬${parseFloat(item.price).toFixed(2)} each</small>
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-sm btn-outline-secondary qty-btn" 
                      onclick="CartService.updateQuantity(${item.cart_item_id}, ${item.quantity - 1})">-</button>
              <input type="number" class="form-control qty-input" value="${item.quantity}" readonly>
              <button class="btn btn-sm btn-outline-secondary qty-btn" 
                      onclick="CartService.updateQuantity(${item.cart_item_id}, ${item.quantity + 1})">+</button>
            </div>
          </td>
          <td>â‚¬${parseFloat(item.price).toFixed(2)}</td>
          <td><strong>â‚¬${itemTotal.toFixed(2)}</strong></td>
          <td>
            <button class="btn btn-danger btn-sm" onclick="CartService.removeItem(${item.cart_item_id})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      `;
    }).join('');

    if (tbody.length) {
      tbody.html(html);
      $('#cartSubtotal').text(`â‚¬${subtotal.toFixed(2)}`);
      $('#cartFinalTotal').text(`â‚¬${subtotal.toFixed(2)}`);
    }
  },

  /**
   * Render empty cart message
   */
  renderEmptyCart: function() {
    const container = $('#cartContent');
    const emptyDiv = $('#cartEmpty');
    
    if (container.length) container.hide();
    if (emptyDiv.length) emptyDiv.show();
  }
};
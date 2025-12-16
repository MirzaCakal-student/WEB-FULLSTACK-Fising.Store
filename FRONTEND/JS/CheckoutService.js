/*finished*/
/* ===================================
   CHECKOUT SERVICE - Multi-step checkout
=================================== */

const CheckoutService = {
  
  currentStep: 1,
  checkoutData: {
    personalInfo: {},
    shippingInfo: {},
    cartItems: [],
    totalAmount: 0
  },

  /**
   * Initialize checkout
   */
  init: function() {
    console.log('✅ CheckoutService initialized');
    
    if (!AuthService.isLoggedIn()) {
      Utils.showToast('Please login to checkout', 'warning');
      window.location.hash = "#login";
      return;
    }

    // Load cart items
    this.loadCartForCheckout();
    
    // Pre-fill personal info from user
    this.prefillPersonalInfo();
    
    // Setup event listeners
    this.setupEventListeners();
    
    // Show step 1
    this.showStep(1);
  },

  /**
   * Setup all event listeners
   */
  setupEventListeners: function() {
    const self = this;

    // Personal Info Form
    $(document).off('submit', '#personalInfoForm').on('submit', '#personalInfoForm', function(e) {
      e.preventDefault();
      self.goToStep(2);
    });

    // Shipping Address Form
    $(document).off('submit', '#shippingAddressForm').on('submit', '#shippingAddressForm', function(e) {
      e.preventDefault();
      self.goToStep(3);
    });

    // Next buttons
    $(document).off('click', '[data-next]').on('click', '[data-next]', function(e) {
      e.preventDefault();
      const nextStep = parseInt($(this).data('next'));
      self.goToStep(nextStep);
    });

    // Back buttons
    $(document).off('click', '[data-back]').on('click', '[data-back]', function(e) {
      e.preventDefault();
      const backStep = parseInt($(this).data('back'));
      self.showStep(backStep);
    });

    // Final pay button
    $(document).off('click', '#finishOrder').on('click', '#finishOrder', function(e) {
      e.preventDefault();
      self.processPayment();
    });
  },

  /**
   * Load cart items for checkout
   */
  loadCartForCheckout: function() {
    const self = this;
    
    RestClient.get('cart', function(response) {
      if (response.success && response.data) {
        self.checkoutData.cartItems = response.data;
        self.calculateTotal();
      }
    });
  },

  /**
   * Calculate total amount
   */
  calculateTotal: function() {
    let total = 0;
    this.checkoutData.cartItems.forEach(item => {
      total += parseFloat(item.price) * parseInt(item.quantity);
    });
    this.checkoutData.totalAmount = total;
  },

  /**
   * Prefill personal info from current user
   */
  prefillPersonalInfo: function() {
    const user = AuthService.getCurrentUser();
    if (user) {
      $('#chEmail').val(user.email);

      // Split username into first and last name
      const nameParts = (user.username || '').trim().split(' ');
      if (nameParts.length >= 1) {
        $('#chFirstName').val(nameParts[0]);
      }
      if (nameParts.length >= 2) {
        $('#chLastName').val(nameParts.slice(1).join(' '));
      }
    }
  },

  /**
   * Go to specific step with validation
   */
  goToStep: function(step) {
    console.log('goToStep called - current:', this.currentStep, 'target:', step);

    // Validate current step before proceeding
    const isValid = this.validateStep(this.currentStep);
    console.log('Validation result:', isValid);

    if (!isValid) {
      console.log('Validation failed, staying on step', this.currentStep);
      return;
    }

    // Save current step data
    this.saveStepData(this.currentStep);
    console.log('Step data saved, moving to step', step);

    // Show next step
    this.showStep(step);
  },

  /**
   * Show specific step
   */
  showStep: function(step) {
    this.currentStep = step;
    
    // Hide all steps
    $('.checkout-step').removeClass('active');
    
    // Show current step
    $(`#step${step}`).addClass('active');
    
    // Update progress circles
    $('.step-circle').removeClass('active');
    $(`.step-circle[data-step="${step}"]`).addClass('active');
    
    // Populate step 3 review if needed
    if (step === 3) {
      this.populateReview();
    }
  },

  /**
   * Validate step data
   */
  validateStep: function(step) {
    switch(step) {
      case 1:
        return this.validatePersonalInfo();
      case 2:
        return this.validateShippingInfo();
      case 3:
        return true; // Review step, no validation needed
      case 4:
        return true; // Payment step
      default:
        return true;
    }
  },

  /**
   * Validate personal info
   */
  validatePersonalInfo: function() {
    const firstName = $('#chFirstName').val().trim();
    const lastName = $('#chLastName').val().trim();
    const email = $('#chEmail').val().trim();
    const phone = $('#chPhone').val().trim();

    console.log('Validating personal info:', { firstName, lastName, email, phone });

    if (!firstName) {
      Utils.showToast('Please enter your first name', 'warning');
      $('#chFirstName').addClass('is-invalid');
      return false;
    }

    if (!lastName) {
      Utils.showToast('Please enter your last name', 'warning');
      $('#chLastName').addClass('is-invalid');
      return false;
    }

    if (!email || !this.isValidEmail(email)) {
      Utils.showToast('Please enter a valid email', 'warning');
      $('#chEmail').addClass('is-invalid');
      return false;
    }

    // Check if email exists in database (must match logged-in user)
    const user = AuthService.getCurrentUser();
    console.log('Current user:', user);

    if (!user) {
      Utils.showToast('Please log in to continue', 'error');
      window.location.hash = '#login';
      return false;
    }

    if (email !== user.email) {
      Utils.showToast('Email must match your registered account email: ' + user.email, 'error');
      $('#chEmail').addClass('is-invalid');
      return false;
    }

    if (!phone) {
      Utils.showToast('Please enter your phone number', 'warning');
      $('#chPhone').addClass('is-invalid');
      return false;
    }

    // Remove invalid classes
    $('#chFirstName, #chLastName, #chEmail, #chPhone').removeClass('is-invalid').addClass('is-valid');

    console.log('Personal info validation passed');
    return true;
  },

  /**
   * Validate shipping info
   */
  validateShippingInfo: function() {
    const address = $('#chAddr').val().trim();
    const city = $('#chCity').val().trim();
    const zip = $('#chZip').val().trim();
    const country = $('#chCountry').val();

    if (!country) {
      Utils.showToast('Please select your country', 'warning');
      $('#chCountry').addClass('is-invalid');
      return false;
    }

    if (!city) {
      Utils.showToast('Please enter your city', 'warning');
      $('#chCity').addClass('is-invalid');
      return false;
    }

    if (!address) {
      Utils.showToast('Please enter your street address', 'warning');
      $('#chAddr').addClass('is-invalid');
      return false;
    }

    if (!zip) {
      Utils.showToast('Please enter ZIP/Postal code', 'warning');
      $('#chZip').addClass('is-invalid');
      return false;
    }

    // Remove invalid classes
    $('#chCountry, #chCity, #chAddr, #chZip').removeClass('is-invalid').addClass('is-valid');

    return true;
  },

  /**
   * Save step data
   */
  saveStepData: function(step) {
    switch(step) {
      case 1:
        this.checkoutData.personalInfo = {
          firstName: $('#chFirstName').val().trim(),
          lastName: $('#chLastName').val().trim(),
          email: $('#chEmail').val().trim(),
          phone: $('#chPhone').val().trim()
        };
        break;
      case 2:
        this.checkoutData.shippingInfo = {
          country: $('#chCountry').val(),
          city: $('#chCity').val().trim(),
          address: $('#chAddr').val().trim(),
          zip: $('#chZip').val().trim()
        };
        break;
    }
  },

  /**
   * Populate review step
   */
  populateReview: function() {
    const tbody = $('#reviewTbody');
    if (!tbody.length) return;

    const self = this;
    let subtotal = 0;
    const shippingCost = 5.00;

    const html = this.checkoutData.cartItems.map(item => {
      const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
      subtotal += itemTotal;
      const imageUrl = Utils.getImageUrl(item.image_url) || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22%3E%3Crect width=%2260%22 height=%2260%22 fill=%22%23ddd%22/%3E%3C/svg%3E';
      const placeholderUrl = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22%3E%3Crect width=%2260%22 height=%2260%22 fill=%22%23ddd%22/%3E%3C/svg%3E';

      return `
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <img src="${imageUrl}"
                   onerror="this.src='${placeholderUrl}'"
                   style="width: 60px; height: 60px; object-fit: cover; margin-right: 10px; border-radius: 6px;">
              <span>${item.name}</span>
            </div>
          </td>
          <td>€${parseFloat(item.price).toFixed(2)}</td>
          <td>
            <div class="d-flex align-items-center gap-1" style="max-width: 120px;">
              <button class="btn btn-sm btn-outline-secondary" onclick="CheckoutService.updateCartQty(${item.cart_item_id}, ${item.quantity - 1})">-</button>
              <input type="number" class="form-control form-control-sm text-center" value="${item.quantity}" readonly style="width: 50px;">
              <button class="btn btn-sm btn-outline-secondary" onclick="CheckoutService.updateCartQty(${item.cart_item_id}, ${item.quantity + 1})">+</button>
            </div>
          </td>
          <td><strong>€${itemTotal.toFixed(2)}</strong></td>
          <td>
            <button class="btn btn-sm btn-danger" onclick="CheckoutService.removeCartItem(${item.cart_item_id})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      `;
    }).join('');

    tbody.html(html);

    // Update totals
    const total = subtotal + shippingCost;
    $('#reviewSubtotal').text(`€${subtotal.toFixed(2)}`);
    $('#reviewShipping').text(`€${shippingCost.toFixed(2)}`);
    $('#reviewTotal').text(`€${total.toFixed(2)}`);

    // Also update step 4 totals
    $('#finalSubtotal').text(`€${subtotal.toFixed(2)}`);
    $('#finalShipping').text(`€${shippingCost.toFixed(2)}`);
    $('#finalTotal').text(`€${total.toFixed(2)}`);

    // Update personal info display
    const pInfo = this.checkoutData.personalInfo;
    $('#reviewPersonalInfo').html(`
      <strong>${pInfo.firstName} ${pInfo.lastName}</strong><br>
      ${pInfo.email}<br>
      ${pInfo.phone}
    `);

    // Update shipping info display
    const sInfo = this.checkoutData.shippingInfo;
    $('#reviewShippingInfo').html(`
      ${sInfo.address}<br>
      ${sInfo.city}, ${sInfo.zip}<br>
      ${sInfo.country}
    `);

    // Store updated total
    this.checkoutData.totalAmount = total;
  },

  /**
   * Update cart item quantity during checkout
   */
  updateCartQty: function(cartItemId, newQty) {
    if (newQty < 1) {
      this.removeCartItem(cartItemId);
      return;
    }

    const self = this;
    Utils.blockUI('Updating...');

    RestClient.put(`cart/${cartItemId}`, { quantity: newQty }, function(response) {
      Utils.unblockUI();
      // Reload cart and refresh review
      self.loadCartForCheckout();
      setTimeout(() => self.populateReview(), 500);
    }, function(error) {
      Utils.unblockUI();
      Utils.showToast('Failed to update quantity', 'error');
    });
  },

  /**
   * Remove cart item during checkout
   */
  removeCartItem: function(cartItemId) {
    if (!confirm('Remove this item from cart?')) return;

    const self = this;
    Utils.blockUI('Removing...');

    RestClient.delete(`cart/${cartItemId}`, null, function(response) {
      Utils.unblockUI();
      Utils.showToast('Item removed', 'info');
      // Reload cart and refresh review
      self.loadCartForCheckout();
      setTimeout(() => self.populateReview(), 500);
    }, function(error) {
      Utils.unblockUI();
      Utils.showToast('Failed to remove item', 'error');
    });
  },

  /**
   * Process payment
   */
  processPayment: function() {
    const self = this;
    const user = AuthService.getCurrentUser();
    const paymentMethod = $('input[name="payMethod"]:checked').val();

    if (this.checkoutData.cartItems.length === 0) {
      Utils.showToast('Your cart is empty', 'warning');
      return;
    }

    Utils.blockUI('Processing payment...');

    // Prepare checkout data
    const checkoutData = {
      email: this.checkoutData.personalInfo.email,
      firstName: this.checkoutData.personalInfo.firstName,
      lastName: this.checkoutData.personalInfo.lastName,
      phone: this.checkoutData.personalInfo.phone,
      address: this.checkoutData.shippingInfo.address,
      city: this.checkoutData.shippingInfo.city,
      zip: this.checkoutData.shippingInfo.zip,
      country: this.checkoutData.shippingInfo.country,
      paymentMethod: paymentMethod
    };

    // Call backend checkout endpoint
    RestClient.post('checkout', checkoutData, function(response) {
      Utils.unblockUI();

      if (response.success) {
        // Show success modal
        $('#confirmEmail').text(self.checkoutData.personalInfo.email);
        $('#confirmOrderId').text(response.data.order_id);

        const modal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
        modal.show();

        Utils.showToast('Order placed successfully!', 'success');
      } else {
        Utils.showToast(response.message || 'Checkout failed', 'error');
      }
    }, function(xhr) {
      Utils.unblockUI();
      const message = xhr.responseJSON?.message || 'Failed to complete checkout';
      Utils.showToast(message, 'error');
    });
  },

  /**
   * Finish checkout and return to shopping
   */
  finishCheckout: function() {
    // Clear checkout data
    this.checkoutData = {
      personalInfo: {},
      shippingInfo: {},
      cartItems: [],
      totalAmount: 0
    };
    this.currentStep = 1;

    // Hide modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentSuccessModal'));
    if (modal) modal.hide();

    // Redirect to home
    window.location.hash = "#home";
    Utils.showToast('Thank you for your purchase!', 'success');
  },

  /**
   * Validate email format
   */
  isValidEmail: function(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }
};
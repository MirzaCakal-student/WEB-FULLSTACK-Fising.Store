/*finished*/
/* ===================================
   UTILS.JS - Helper Functions
=================================== */

const Utils = {
  
  /**
   * Parse JWT token to extract payload
   */
  parseJwt: function(token) {
    if (!token) return null;

    try {
      const payload = token.split('.')[1];
      const decoded = atob(payload);
      return JSON.parse(decoded);
    } catch (e) {
      console.error("Invalid JWT token", e);
      return null;
    }
  },

  /**
   * Get properly formatted image URL
   * Handles relative paths and ensures correct base path
   */
  getImageUrl: function(imageUrl) {
    if (!imageUrl) {
      return 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22200%22%3E%3Crect width=%22300%22 height=%22200%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%23999%22%3ENo Image%3C/text%3E%3C/svg%3E';
    }

    // If already a full URL (http/https), return as-is
    if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://') || imageUrl.startsWith('data:')) {
      return imageUrl;
    }

    // If it's a relative path (starts with assets/), prepend the FRONTEND base
    if (imageUrl.startsWith('assets/')) {
      const frontendBase = Constants.PROJECT_BASE_URL.replace('/BACKEND/', '/FRONTEND/');
      return frontendBase + imageUrl;
    }

    // If it's just a filename (e.g., "rod1.jpg"), assume it's in FRONTEND/assets/products/
    if (!imageUrl.includes('/') && !imageUrl.startsWith('.')) {
      const frontendBase = Constants.PROJECT_BASE_URL.replace('/BACKEND/', '/FRONTEND/');
      return frontendBase + 'assets/products/' + imageUrl;
    }

    // Otherwise return as-is
    return imageUrl;
  },

  /**
   * Get placeholder image for error fallback
   */
  getPlaceholderImage: function() {
    return 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22200%22%3E%3Crect width=%22300%22 height=%22200%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%23999%22%3ENo Image%3C/text%3E%3C/svg%3E';
  },

  /**
   * Update navbar badges (cart and wishlist counts)
   */
  updateBadges: function(cartCount = 0, wishlistCount = 0) {
    $('#cart-count').text(cartCount);
    $('#wishlist-count').text(wishlistCount);
  },

  /**
   * Show/hide navbar items based on login status
   */
  updateNavbar: function(isLoggedIn, user = null) {
    if (isLoggedIn && user) {
      // Hide login/signup
      $('#loginNav, #signupNav').hide();

      // Show admin link if user is admin
      if (user.role === Constants.ADMIN_ROLE) {
        // Admin can only see: Home, Products, Admin Panel, Profile, Logout
        $('#adminNav, #userNav, #logoutNav').show();
        // Hide cart/wishlist/checkout/about for admin
        $('#cartNav, #wishlistNav, #checkoutNav, #aboutNav').hide();
      } else {
        // Regular user navigation
        $('#adminNav').hide();
        // Show all user items including About
        $('#userNav, #logoutNav, #cartNav, #wishlistNav, #checkoutNav, #aboutNav').show();
      }
    } else {
      // Not logged in - show login/signup and About
      $('#loginNav, #signupNav, #aboutNav').show();

      // Hide logged-in items
      $('#userNav, #logoutNav, #cartNav, #wishlistNav, #checkoutNav, #adminNav').hide();
    }
  },

  /**
   * Show toast notification
   */
  showToast: function(message, type = 'success') {
    if (typeof toastr !== 'undefined') {
      toastr[type](message);
    } else {
      console.log(`[${type.toUpperCase()}] ${message}`);
    }
  },

  /**
   * Block UI with loading message
   */
  blockUI: function(message = 'Processing...') {
    if (typeof $.blockUI !== 'undefined') {
      $.blockUI({ 
        message: `<h3><i class="bi bi-hourglass-split"></i> ${message}</h3>` 
      });
    }
  },

  /**
   * Unblock UI
   */
  unblockUI: function() {
    if (typeof $.unblockUI !== 'undefined') {
      $.unblockUI();
    }
  },

  /**
   * Get current user from localStorage
   */
  getCurrentUser: function() {
    const userStr = localStorage.getItem("user");
    if (userStr) {
      try {
        return JSON.parse(userStr);
      } catch (e) {
        return null;
      }
    }
    return null;
  },

  /**
   * Logout user
   */
  logout: function() {
    localStorage.removeItem("user_token");
    localStorage.removeItem("user");
    
    this.updateNavbar(false);
    this.updateBadges(0, 0);
    this.showToast('Logged out successfully', 'info');
    
    window.location.hash = "#home";
  }
};
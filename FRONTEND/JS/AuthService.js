/*finished*/
/* ===================================
   AUTH SERVICE - Login/Logout/Register
=================================== */

const AuthService = {

  /**
   * Initialize auth state
   */
  init: function() {
    console.log('✅ AuthService initialized');
    
    // Check if user is already logged in
    const token = localStorage.getItem("user_token");
    const userStr = localStorage.getItem("user");
    
    if (token && userStr) {
      try {
        const user = JSON.parse(userStr);
        Utils.updateNavbar(true, user);
        console.log('👤 User logged in:', user.email);
        
        // Load cart and wishlist counts if on products/cart/wishlist pages
        if (typeof CartService !== 'undefined') {
          CartService.loadCartCount();
        }
        if (typeof WishlistService !== 'undefined') {
          WishlistService.loadWishlistCount();
        }
      } catch (e) {
        console.error('Invalid user data in localStorage');
        this.logout(false); // logout without confirmation
      }
    } else {
      Utils.updateNavbar(false);
    }
    
    // Setup login form handler
    $(document).off('submit', '#loginForm').on('submit', '#loginForm', function(e) {
      e.preventDefault();
      AuthService.handleLogin(this);
    });
    
    // Setup signup form handler
    $(document).off('submit', '#signupForm').on('submit', '#signupForm', function(e) {
      e.preventDefault();
      AuthService.handleSignup(this);
    });
  },

  /**
   * Handle login form submission
   */
  handleLogin: function(form) {
    const email = $(form).find('#loginEmail').val().trim();
    const password = $(form).find('#loginPassword').val();

    if (!email || !password) {
      Utils.showToast('Please enter email and password', 'warning');
      return;
    }

    Utils.blockUI('Logging in...');

    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/login",
      type: "POST",
      data: JSON.stringify({ email, password }),
      contentType: "application/json",
      dataType: "json",
      success: function(result) {
        Utils.unblockUI();
        
        if (result.success && result.data && result.data.token) {
          // Store token and user
          localStorage.setItem("user_token", result.data.token);
          localStorage.setItem("user", JSON.stringify(result.data.user));
          
          console.log('✅ Login successful, token stored');
          console.log('👤 User:', result.data.user);
          console.log('🔑 Token:', result.data.token.substring(0, 50) + '...');
          
          Utils.showToast('Login successful!', 'success');
          Utils.updateNavbar(true, result.data.user);
          
          // Redirect to home page
          setTimeout(() => {
            window.location.hash = "#home";
          }, 500);
        } else {
          Utils.showToast(result.message || 'Login failed', 'error');
        }
      },
      error: function(xhr) {
        Utils.unblockUI();
        const message = xhr.responseJSON?.message || xhr.responseText || 'Login failed';
        console.error('Login error:', message);
        Utils.showToast(message, 'error');
      }
    });
  },

  /**
   * Handle signup form submission
   */
  handleSignup: function(form) {
    const username = $(form).find('#signupName').val().trim() + ' ' + $(form).find('#signupSurname').val().trim();
    const email = $(form).find('#signupEmail').val().trim();
    const password = $(form).find('#signupPassword').val();

    if (!email || !password || !username) {
      Utils.showToast('Please fill all required fields', 'warning');
      return;
    }

    Utils.blockUI('Creating account...');

    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/register",
      type: "POST",
      data: JSON.stringify({ username, email, password }),
      contentType: "application/json",
      dataType: "json",
      success: function(result) {
        Utils.unblockUI();
        
        if (result.success && result.data && result.data.token) {
          // Auto-login after successful registration
          localStorage.setItem("user_token", result.data.token);
          localStorage.setItem("user", JSON.stringify(result.data.user));
          
          console.log('✅ Registration successful, token stored');
          console.log('👤 User:', result.data.user);
          console.log('🔑 Token:', result.data.token.substring(0, 50) + '...');
          
          Utils.showToast('Account created successfully!', 'success');
          Utils.updateNavbar(true, result.data.user);
          
          // Redirect to home page
          setTimeout(() => {
            window.location.hash = "#home";
          }, 500);
        } else {
          Utils.showToast(result.message || 'Registration failed', 'error');
        }
      },
      error: function(xhr) {
        Utils.unblockUI();
        const message = xhr.responseJSON?.message || xhr.responseText || 'Registration failed';
        console.error('Registration error:', message);
        Utils.showToast(message, 'error');
      }
    });
  },

  /**
   * Logout user with confirmation
   */
  logout: function(askConfirmation = true) {
    // Ask for confirmation
    if (askConfirmation && !confirm('Are you sure you want to logout?')) {
      return;
    }

    localStorage.removeItem("user_token");
    localStorage.removeItem("user");
    
    Utils.updateNavbar(false);
    Utils.updateBadges(0, 0);
    Utils.showToast('Logged out successfully', 'info');
    
    window.location.hash = "#home";
  },

  /**
   * Check if user is logged in
   */
  isLoggedIn: function() {
    const token = localStorage.getItem("user_token");
    const user = localStorage.getItem("user");
    return !!(token && user);
  },

  /**
   * Get current user
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
  }
};
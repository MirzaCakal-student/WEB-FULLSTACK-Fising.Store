/* ===================================
   ADMIN SERVICE - Admin Panel
=================================== */

const AdminService = {

  /**
   * Initialize admin panel
   */
  init: function() {
    console.log('âœ… AdminService initialized');
    
    const user = AuthService.getCurrentUser();
    if (!user || user.role !== Constants.ADMIN_ROLE) {
      Utils.showToast('Access denied: Admin only', 'error');
      window.location.hash = "#home";
      return;
    }

    this.loadProducts();
    this.loadUsers();
    this.loadOrders();
    this.loadPayments();
    this.setupEventHandlers();
  },

  /**
   * Setup event handlers
   */
  setupEventHandlers: function() {
    // Product form
    $(document).off('submit', '#productForm').on('submit', '#productForm', function(e) {
      e.preventDefault();
      AdminService.saveProduct();
    });

    // User form
    $(document).off('submit', '#userForm').on('submit', '#userForm', function(e) {
      e.preventDefault();
      AdminService.saveUser();
    });
  },

  /**
   * Load all products
   */
  loadProducts: function() {
    RestClient.get('products', function(response) {
      if (response.success && response.data) {
        AdminService.renderProductsTable(response.data);
      }
    });
  },

  /**
   * Render products table
   */
  renderProductsTable: function(products) {
    const tbody = $('#productsTableBody');
    if (!tbody.length) return;

    const html = products.map(p => {
      const imageUrl = Utils.getImageUrl(p.image_url);

      return `
      <tr>
        <td>${p.product_id}</td>
        <td><img src="${imageUrl}" style="width:50px;height:50px;object-fit:cover;border-radius:4px;" onerror="this.src='${Utils.getPlaceholderImage()}'"></td>
        <td>${p.name}</td>
        <td>${p.category}</td>
        <td>${parseFloat(p.price).toFixed(2)}</td>
        <td>${p.stock_quantity}</td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="AdminService.editProduct(${p.product_id})">
            <i class="bi bi-pencil"></i> Edit
          </button>
          <button class="btn btn-sm btn-danger" onclick="AdminService.deleteProduct(${p.product_id})">
            <i class="bi bi-trash"></i> Delete
          </button>
        </td>
      </tr>
    `;
    }).join('');

    tbody.html(html);
  },

  /**
   * Open add product modal
   */
  openAddProductModal: function() {
    $('#productModalTitle').text('Add Product');
    $('#product_id').val('');
    $('#product_name').val('');
    $('#product_category').val('Rods');
    $('#product_price').val('');
    $('#product_stock').val('');
    $('#product_image').val('');
    
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
  },

  /**
   * Edit product
   */
  editProduct: function(productId) {
    RestClient.get(`products/${productId}`, function(response) {
      if (response.success && response.data) {
        const p = response.data;
        
        $('#productModalTitle').text('Edit Product');
        $('#product_id').val(p.product_id);
        $('#product_name').val(p.name);
        $('#product_category').val(p.category);
        $('#product_price').val(p.price);
        $('#product_stock').val(p.stock_quantity);
        $('#product_image').val(p.image_url);
        
        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
      }
    });
  },

  /**
   * Save product (add or update)
   */
  saveProduct: function() {
    const productId = $('#product_id').val();
    const data = {
      name: $('#product_name').val(),
      category: $('#product_category').val(),
      price: parseFloat($('#product_price').val()),
      stock_quantity: parseInt($('#product_stock').val()),
      image_url: $('#product_image').val()
    };

    if (!data.name || !data.price || !data.stock_quantity) {
      Utils.showToast('Please fill all required fields', 'warning');
      return;
    }

    Utils.blockUI('Saving product...');

    if (productId) {
      // Update
      RestClient.put(`products/${productId}`, data, function(response) {
        Utils.unblockUI();
        Utils.showToast('Product updated!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        AdminService.loadProducts();
      }, function(xhr) {
        Utils.unblockUI();
        Utils.showToast('Failed to update product', 'error');
      });
    } else {
      // Create
      RestClient.post('products', data, function(response) {
        Utils.unblockUI();
        Utils.showToast('Product created!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        AdminService.loadProducts();
      }, function(xhr) {
        Utils.unblockUI();
        Utils.showToast('Failed to create product', 'error');
      });
    }
  },

  /**
   * Delete product
   */
  deleteProduct: function(productId) {
    if (!confirm('Delete this product?')) return;

    Utils.blockUI('Deleting...');

    RestClient.delete(`products/${productId}`, null, function(response) {
      Utils.unblockUI();
      Utils.showToast('Product deleted', 'success');
      AdminService.loadProducts();
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast('Failed to delete product', 'error');
    });
  },

  /**
   * Load all users
   */
  loadUsers: function() {
    RestClient.get('users', function(response) {
      if (response.success && response.data) {
        AdminService.renderUsersTable(response.data);
      }
    });
  },

  /**
   * Render users table
   */
  renderUsersTable: function(users) {
    const tbody = $('#usersTableBody');
    if (!tbody.length) return;

    const html = users.map(u => `
      <tr>
        <td>${u.user_id}</td>
        <td>${u.username || 'N/A'}</td>
        <td>${u.email}</td>
        <td><span class="badge bg-${u.role === 'admin' ? 'danger' : 'primary'}">${u.role}</span></td>
        <td>${u.created_at || 'N/A'}</td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="AdminService.editUser(${u.user_id})">
            <i class="bi bi-pencil"></i> Edit
          </button>
          <button class="btn btn-sm btn-danger" onclick="AdminService.deleteUser(${u.user_id})">
            <i class="bi bi-trash"></i> Delete
          </button>
        </td>
      </tr>
    `).join('');

    tbody.html(html);
  },

  /**
   * Open add user modal
   */
  openAddUserModal: function() {
    $('#userModalTitle').text('Add User');
    $('#user_id').val('');
    $('#user_name').val('');
    $('#user_email').val('');
    $('#user_password').val('');
    $('#user_role').val('user');
    
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
  },

  /**
   * Edit user
   */
  editUser: function(userId) {
    RestClient.get(`users/${userId}`, function(response) {
      if (response.success && response.data) {
        const u = response.data;
        
        $('#userModalTitle').text('Edit User');
        $('#user_id').val(u.user_id);
        $('#user_name').val(u.username);
        $('#user_email').val(u.email);
        $('#user_password').val('');
        $('#user_role').val(u.role);
        
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        modal.show();
      }
    });
  },

  /**
   * Save user (add or update)
   */
  saveUser: function() {
    const userId = $('#user_id').val();
    const data = {
      username: $('#user_name').val(),
      email: $('#user_email').val(),
      role: $('#user_role').val()
    };

    const password = $('#user_password').val();
    if (password) {
      data.password = password;
    }

    if (!data.username || !data.email) {
      Utils.showToast('Please fill all required fields', 'warning');
      return;
    }

    Utils.blockUI('Saving user...');

    if (userId) {
      // Update
      RestClient.put(`users/${userId}`, data, function(response) {
        Utils.unblockUI();
        Utils.showToast('User updated!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        AdminService.loadUsers();
      }, function(xhr) {
        Utils.unblockUI();
        Utils.showToast('Failed to update user', 'error');
      });
    } else {
      // Create
      if (!password) {
        Utils.showToast('Password is required for new users', 'warning');
        Utils.unblockUI();
        return;
      }
      
      RestClient.post('users', data, function(response) {
        Utils.unblockUI();
        Utils.showToast('User created!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        AdminService.loadUsers();
      }, function(xhr) {
        Utils.unblockUI();
        Utils.showToast('Failed to create user', 'error');
      });
    }
  },

  /**
   * Delete user
   */
  deleteUser: function(userId) {
    if (!confirm('Delete this user?')) return;

    Utils.blockUI('Deleting...');

    RestClient.delete(`users/${userId}`, null, function(response) {
      Utils.unblockUI();
      Utils.showToast('User deleted', 'success');
      AdminService.loadUsers();
    }, function(xhr) {
      Utils.unblockUI();
      Utils.showToast('Failed to delete user', 'error');
    });
  },

  /**
   * Load all orders
   */
  loadOrders: function() {
    RestClient.get('orders', function(response) {
      if (response.success && response.data) {
        AdminService.renderOrdersTable(response.data);
      }
    });
  },

  /**
   * Render orders table
   */
  renderOrdersTable: function(orders) {
    const tbody = $('#ordersTableBody');
    if (!tbody.length) return;

    const html = orders.map(o => `
      <tr>
        <td>${o.order_id}</td>
        <td>User #${o.user_id}</td>
        <td>${parseFloat(o.total_amount).toFixed(2)}</td>
        <td><span class="badge bg-info">${o.payment_status || 'pending'}</span></td>
        <td>${o.created_at || 'N/A'}</td>
        <td>
          <button class="btn btn-sm btn-info" onclick="AdminService.viewOrder(${o.order_id})">
            <i class="bi bi-eye"></i> View
          </button>
        </td>
      </tr>
    `).join('');

    tbody.html(html || '<tr><td colspan="6" class="text-center">No orders yet</td></tr>');
  },

  /**
   * View order details
   */
  viewOrder: function(orderId) {
    Utils.showToast('Order details feature coming soon', 'info');
  },

  /**
   * Load all payments
   */
  loadPayments: function() {
    RestClient.get('payments', function(response) {
      if (response.success && response.data) {
        AdminService.renderPaymentsTable(response.data);
      }
    });
  },

  /**
   * Render payments table
   */
  renderPaymentsTable: function(payments) {
    const tbody = $('#paymentsTableBody');
    if (!tbody.length) return;

    const html = payments.map(p => `
      <tr>
        <td>${p.payment_id}</td>
        <td>Order #${p.order_id}</td>
        <td>${parseFloat(p.amount).toFixed(2)}</td>
        <td>${p.payment_method || 'N/A'}</td>
        <td><span class="badge bg-success">${p.status || 'completed'}</span></td>
        <td>${p.created_at || 'N/A'}</td>
      </tr>
    `).join('');

    tbody.html(html || '<tr><td colspan="6" class="text-center">No payments yet</td></tr>');
  }
};
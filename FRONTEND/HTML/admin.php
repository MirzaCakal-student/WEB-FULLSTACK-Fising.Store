<?php
require_once __DIR__ . '/../../backend/rest/DAO/ProductDAO.php';
$productDAO = new ProductDAO();

// CRUD actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add'])) {
    $productDAO->create($_POST['name'], $_POST['category'], $_POST['price'], $_POST['stock_quantity'], $_POST['image_url']);
  } elseif (isset($_POST['update'])) {
    $productDAO->update($_POST['product_id'], $_POST['name'], $_POST['category'], $_POST['price'], $_POST['stock_quantity'], $_POST['image_url']);
  } elseif (isset($_POST['delete'])) {
    $productDAO->delete($_POST['product_id']);
  }
}

$products = $productDAO->getAll();
?>

<h3 class="fw-bold mb-3">Admin Panel</h3>
<div class="alert alert-warning">Admins can edit products below.</div>

<!-- Add new product -->
<form method="POST" class="row g-2 mb-4">
  <div class="col-md-2"><input name="name" class="form-control" placeholder="Name" required></div>
  <div class="col-md-2"><input name="category" class="form-control" placeholder="Category" required></div>
  <div class="col-md-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Price" required></div>
  <div class="col-md-2"><input name="stock_quantity" type="number" class="form-control" placeholder="Qty" required></div>
  <div class="col-md-3"><input name="image_url" class="form-control" placeholder="Image URL"></div>
  <div class="col-md-1"><button name="add" class="btn btn-success w-100">Add</button></div>
</form>

<div class="table-responsive">
  <table class="table align-middle">
    <thead><tr><th>#</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Qty</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <form method="POST">
          <td><?= htmlspecialchars($p['product_id']) ?>
              <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
          </td>
          <td><img src="<?= htmlspecialchars($p['image_url']) ?>" width="60"></td>
          <td><input name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($p['name']) ?>"></td>
          <td><input name="category" class="form-control form-control-sm" value="<?= htmlspecialchars($p['category']) ?>"></td>
          <td><input name="price" type="number" step="0.01" class="form-control form-control-sm" value="<?= $p['price'] ?>"></td>
          <td><input name="stock_quantity" type="number" class="form-control form-control-sm" value="<?= $p['stock_quantity'] ?>"></td>
          <td>
            <input type="hidden" name="image_url" value="<?= htmlspecialchars($p['image_url']) ?>">
            <button name="update" class="btn btn-sm btn-primary">Save</button>
            <button name="delete" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

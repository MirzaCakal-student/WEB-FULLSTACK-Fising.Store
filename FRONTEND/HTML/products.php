<?php
require_once __DIR__ . '/../../backend/rest/DAO/ProductDAO.php';
$productDAO = new ProductDAO();
$products = $productDAO->getAll();
?>

<div class="container py-4">
  <h2 class="fw-bold text-success mb-4">Our Products</h2>

  <!-- Filters -->
  <div class="row mb-4">
    <div class="col-md-8">
      <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search products..." oninput="filterProducts()">
    </div>
    <div class="col-md-4">
      <select id="categoryFilter" class="form-select" onchange="filterProducts()">
        <option value="">All Categories</option>
        <option value="Rods">Rods</option>
        <option value="Reels">Reels</option>
        <option value="Baits">Baits</option>
        <option value="Accessories">Accessories</option>
      </select>
    </div>
  </div>

  <!-- Products Grid -->
  <div class="row" id="productsContainer">
    <?php foreach ($products as $p): ?>
    <div class="col-md-4 mb-4 product-card" data-category="<?= htmlspecialchars($p['category']) ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>">
      <div class="card h-100 shadow-sm">
        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="card-img-top" alt="">
        <div class="card-body text-center">
          <h5><?= htmlspecialchars($p['name']) ?></h5>
          <p class="text-muted"><?= htmlspecialchars($p['category']) ?></p>
          <p class="fw-bold text-success">$<?= number_format($p['price'], 2) ?></p>
          <form method="POST" action="cart_add.php">
            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
            <button class="btn btn-primary w-100">Add to Cart</button>
          </form>
          <form method="POST" action="wishlist_add.php" class="mt-2">
            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
            <button class="btn btn-outline-danger w-100">Add to Wishlist</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
function filterProducts() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const category = document.getElementById('categoryFilter').value;
  document.querySelectorAll('.product-card').forEach(card => {
    const matchCategory = !category || card.dataset.category === category;
    const matchName = card.dataset.name.includes(search);
    card.style.display = (matchCategory && matchName) ? '' : 'none';
  });
}
</script>

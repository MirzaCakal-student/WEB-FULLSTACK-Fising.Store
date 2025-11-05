<?php
require_once __DIR__ . '/../../backend/rest/DAO/WishlistItemDAO.php';
$wishlistDAO = new WishlistItemDAO();
$user_id = 2;
$wishlist = $wishlistDAO->getByUser($user_id);
?>

<div class="container py-4">
  <h2 class="fw-bold text-primary mb-4">My Wishlist</h2>

  <div class="row g-4">
    <?php foreach ($wishlist as $w): ?>
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="<?= htmlspecialchars($w['image_url']) ?>" class="card-img-top" alt="">
        <div class="card-body text-center">
          <h5><?= htmlspecialchars($w['name']) ?></h5>
          <p class="text-muted"><?= htmlspecialchars($w['category']) ?></p>
          <p class="fw-bold text-success">$<?= number_format($w['price'], 2) ?></p>
          <form method="POST" action="wishlist_remove.php">
            <input type="hidden" name="wishlist_item_id" value="<?= $w['wishlist_item_id'] ?>">
            <button class="btn btn-outline-danger w-100">Remove</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

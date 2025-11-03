<?php
require_once __DIR__ . '/../../backend/rest/DAO/CartItemDAO.php';
$cartDAO = new CartItemDAO();
$user_id = 2;

$cart = $cartDAO->getByUser($user_id);
$total = $cartDAO->getTotal($user_id);
?>

<div class="container py-4">
  <h2 class="fw-bold text-primary mb-4">Shopping Cart</h2>

  <div class="table-responsive mb-3">
    <table class="table align-middle">
      <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($cart as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['total'], 2) ?></td>
            <td>
              <form method="POST" action="cart_remove.php">
                <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                <button class="btn btn-sm btn-danger">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="text-end fw-bold fs-5">Total: $<?= number_format($total, 2) ?></div>
</div>

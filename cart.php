<?php
require_once __DIR__ . '/includes/auth.php';
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    $_SESSION['flash'] = 'Item removed from cart.';
    header('Location: cart.php'); exit();
}
$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0;
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $qty = $cart[$row['id']] ?? 0;
        $sub = $qty * (float)$row['price'];
        $total += $sub;
        $items[] = [$row, $qty, $sub];
    }
}
require_once __DIR__ . '/includes/layout.php'; render_top('Cart'); flash_message(); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>Your Cart</h2><a class="btn btn-outline-secondary" href="shop.php">Continue Shopping</a></div>
<div class="card p-3">
<?php if (!$items): ?>
  <p class="mb-0">Your cart is empty.</p>
<?php else: ?>
  <div class="table-responsive"><table class="table"><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead><tbody>
  <?php foreach ($items as [$p,$qty,$sub]): ?>
    <tr><td><?= h($p['name']) ?></td><td>Rs. <?= number_format((float)$p['price'],2) ?></td><td><?= (int)$qty ?></td><td>Rs. <?= number_format($sub,2) ?></td><td><a class="btn btn-sm btn-danger" href="?remove=<?= (int)$p['id'] ?>">Remove</a></td></tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <div class="d-flex justify-content-end align-items-center gap-3"><h4 class="mb-0">Total: Rs. <?= number_format($total,2) ?></h4><a class="btn btn-success" href="checkout.php">Proceed to Checkout</a></div>
<?php endif; ?>
</div>
<?php render_bottom(); ?>

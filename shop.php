<?php
require_once __DIR__ . '/includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];

    $stockCheck = $conn->prepare("SELECT COALESCE(stock_count,0) AS stock_count FROM inventory WHERE product_id = ? AND rdc_name = 'Central RDC'");
    $stockCheck->bind_param('i', $productId);
    $stockCheck->execute();
    $stockResult = $stockCheck->get_result()->fetch_assoc();
    $availableStock = (int)($stockResult['stock_count'] ?? 0);
    $currentCartQty = (int)($_SESSION['cart'][$productId] ?? 0);

    if ($availableStock <= $currentCartQty) {
        $_SESSION['flash'] = 'Cannot add more. This product is out of stock or the requested quantity exceeds current stock.';
    } else {
        $_SESSION['cart'][$productId] = $currentCartQty + 1;
        $_SESSION['flash'] = 'Product added to cart.';
    }
    header('Location: shop.php');
    exit();
}
$products = $conn->query("SELECT p.*, COALESCE(i.stock_count,0) AS stock_count
                          FROM products p
                          LEFT JOIN inventory i ON i.product_id = p.id AND i.rdc_name = 'Central RDC'
                          ORDER BY p.created_at DESC");
require_once __DIR__ . '/includes/layout.php'; render_top('Products'); flash_message(); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>Products</h2><a class="btn btn-outline-success" href="cart.php">View Cart</a></div>
<div class="row g-4">
<?php while ($row = $products->fetch_assoc()): ?>
  <div class="col-md-6 col-lg-4 col-xl-3">
    <div class="card h-100">
      <img class="card-img-top product-img" src="<?= h(file_exists(__DIR__ . '/uploads/' . $row['image']) ? 'uploads/' . $row['image'] : 'img/' . $row['image']) ?>" alt="<?= h($row['name']) ?>">
      <div class="card-body d-flex flex-column">
        <span class="badge bg-success-subtle text-success mb-2"><?= h($row['category']) ?></span>
        <h5><?= h($row['name']) ?></h5>
        <p class="text-muted small mb-2">Retailer: <?= h($row['retailer']) ?></p>
        <p class="small"><?= h($row['description']) ?></p>
        <p class="small mb-2"><strong>Available Stock:</strong> <?= (int)$row['stock_count'] ?></p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          <strong>Rs. <?= number_format((float)$row['price'], 2) ?></strong>
          <form method="post">
            <input type="hidden" name="product_id" value="<?= (int)$row['id'] ?>">
            <button class="btn btn-success btn-sm" <?= ((int)$row['stock_count'] <= 0 ? 'disabled' : '') ?>><?= ((int)$row['stock_count'] <= 0 ? 'Out of Stock' : 'Add to Cart') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>
<?php render_bottom(); ?>

<?php
require_once __DIR__ . '/includes/auth.php';
require_role('Retail Customer');
$userId = (int)current_user()['id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC");
require_once __DIR__ . '/includes/layout.php'; render_top('Customer Dashboard'); flash_message(); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>Customer Dashboard</h2><a class="btn btn-success" href="shop.php">Place New Order</a></div>
<div class="row g-4 mb-4">
  <div class="col-md-4"><div class="card p-3"><h6>My Orders</h6><h3><?= $orders->num_rows ?></h3></div></div>
  <div class="col-md-4"><div class="card p-3"><h6>Cart Items</h6><h3><?= array_sum($_SESSION['cart'] ?? []) ?></h3></div></div>
  <div class="col-md-4"><div class="card p-3"><h6>Latest Status</h6><h3><?= h(scalar($conn, "SELECT COALESCE(MAX(status),'No Orders') FROM orders WHERE user_id = $userId")) ?></h3></div></div>
</div>
<div class="card p-4"><h4>My Orders</h4><div class="table-responsive"><table class="table"><thead><tr><th>ID</th><th>Address</th><th>Payment</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php mysqli_data_seek($orders,0); while($row=$orders->fetch_assoc()): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= h($row['address']) ?></td><td><?= h($row['payment_method']) ?></td><td>Rs. <?= number_format((float)$row['total_amount'],2) ?></td><td><?= h($row['status']) ?></td><td><?= h($row['created_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php render_bottom(); ?>

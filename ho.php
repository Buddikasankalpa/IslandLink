<?php
require_once __DIR__ . '/includes/auth.php';
require_role('Head Office');
require_once __DIR__ . '/includes/layout.php'; render_top('Head Office Dashboard');
$totalSales = scalar($conn, 'SELECT COALESCE(SUM(total_amount),0) FROM orders');
$pending = scalar($conn, "SELECT COUNT(*) FROM orders WHERE status='Pending'");
$completed = scalar($conn, "SELECT COUNT(*) FROM orders WHERE status='Completed'");
?>
<h2 class="mb-4">Head Office Dashboard</h2>
<div class="row g-4 mb-4"><div class="col-md-4"><div class="card p-3"><h6>Total Sales</h6><h3>Rs. <?= number_format((float)$totalSales,2) ?></h3></div></div><div class="col-md-4"><div class="card p-3"><h6>Pending Orders</h6><h3><?= $pending ?></h3></div></div><div class="col-md-4"><div class="card p-3"><h6>Completed Orders</h6><h3><?= $completed ?></h3></div></div></div>
<div class="card p-4"><h4>Order Overview</h4><?php $rows = $conn->query('SELECT id, customer_name, total_amount, status FROM orders ORDER BY id DESC LIMIT 8'); ?><table class="table"><thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead><tbody><?php while($r=$rows->fetch_assoc()): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= h($r['customer_name']) ?></td><td>Rs. <?= number_format((float)$r['total_amount'],2) ?></td><td><?= h($r['status']) ?></td></tr><?php endwhile; ?></tbody></table></div>
<?php render_bottom(); ?>

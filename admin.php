<?php
require_once __DIR__ . '/includes/auth.php';
require_role('Admin');
require_once __DIR__ . '/includes/layout.php';
render_top('Admin Dashboard'); flash_message();
$productCount = count_rows($conn, 'products');
$userCount = count_rows($conn, 'users');
$orderCount = count_rows($conn, 'orders');
$deliveryCount = count_rows($conn, 'deliveries');
$totalSales = scalar($conn, 'SELECT COALESCE(SUM(total_amount),0) FROM orders');
?>
<h2 class="mb-4">Admin Dashboard</h2>
<div class="row g-4 mb-4">
  <div class="col-md-3"><div class="card p-3"><h6>Products</h6><h3><?= $productCount ?></h3></div></div>
  <div class="col-md-3"><div class="card p-3"><h6>Users</h6><h3><?= $userCount ?></h3></div></div>
  <div class="col-md-3"><div class="card p-3"><h6>Orders</h6><h3><?= $orderCount ?></h3></div></div>
  <div class="col-md-3"><div class="card p-3"><h6>Total Sales</h6><h3>Rs. <?= number_format((float)$totalSales,2) ?></h3></div></div>
</div>
<div class="row g-4 dashboard-links">
  <div class="col-md-6"><a href="admin_reports.php"><div class="card p-4 h-100"><h4>Report Generation</h4><p>Generate sales performance, stock turnover, and delivery efficiency reports.</p></div></a></div>
  <div class="col-md-6"><div class="card p-4 h-100"><h4>Admin Summary</h4><p>Admin now focuses on monitoring KPIs and reports. Product, order, and user management are handled by the RDC dashboard.</p></div></div>
</div>
<div class="card p-4 mt-4"><h4>Recent Orders</h4>
<?php $recent = $conn->query('SELECT id, customer_name, total_amount, status, created_at FROM orders ORDER BY id DESC LIMIT 5'); ?>
<div class="table-responsive"><table class="table"><thead><tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php while($row=$recent->fetch_assoc()): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= h($row['customer_name']) ?></td><td>Rs. <?= number_format((float)$row['total_amount'],2) ?></td><td><?= h($row['status']) ?></td><td><?= h($row['created_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php render_bottom(); ?>

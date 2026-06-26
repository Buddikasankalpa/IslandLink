<?php
require_once __DIR__ . '/includes/auth.php';
require_role('Admin');
require_once __DIR__ . '/includes/layout.php';

$totalSales = (float)scalar($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status <> 'Cancelled'");
$completedOrders = (int)scalar($conn, "SELECT COUNT(*) FROM orders WHERE status='Completed'");
$pendingOrders = (int)scalar($conn, "SELECT COUNT(*) FROM orders WHERE status='Pending'");
$totalStock = (int)scalar($conn, "SELECT COALESCE(SUM(stock_count),0) FROM inventory");
$unitsSold = (int)scalar($conn, "SELECT COALESCE(SUM(quantity),0) FROM order_items");
$stockTurnover = $totalStock > 0 ? round($unitsSold / $totalStock, 2) : 0;
$deliveredCount = (int)scalar($conn, "SELECT COUNT(*) FROM deliveries WHERE delivery_status='Delivered'");
$deliveryTotal = (int)scalar($conn, "SELECT COUNT(*) FROM deliveries");
$deliveryEfficiency = $deliveryTotal > 0 ? round(($deliveredCount / $deliveryTotal) * 100, 2) : 0;

$salesRows = $conn->query("SELECT o.id, o.customer_name, o.total_amount, o.status, o.created_at FROM orders o ORDER BY o.id DESC");
$stockRows = $conn->query("SELECT p.name, p.category, COALESCE(i.stock_count,0) AS stock_count, COALESCE(s.sold_qty,0) AS sold_qty
                           FROM products p
                           LEFT JOIN inventory i ON i.product_id = p.id AND i.rdc_name = 'Central RDC'
                           LEFT JOIN (
                               SELECT product_id, SUM(quantity) AS sold_qty
                               FROM order_items
                               GROUP BY product_id
                           ) s ON s.product_id = p.id
                           ORDER BY p.name ASC");
$deliveryRows = $conn->query("SELECT d.id, d.order_id, d.driver_name, d.route_details, d.delivery_status, d.created_at FROM deliveries d ORDER BY d.id DESC");

render_top('Admin Reports');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Report Generation</h2>
  <div>
    <button class="btn btn-success" onclick="window.print()">Print / Save PDF</button>
    <a class="btn btn-outline-secondary" href="admin.php">Back</a>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-4"><div class="card p-3"><h5>Sales Performance</h5><p class="mb-1">Total Sales</p><h3>Rs. <?= number_format($totalSales,2) ?></h3><small>Completed Orders: <?= $completedOrders ?> | Pending Orders: <?= $pendingOrders ?></small></div></div>
  <div class="col-md-4"><div class="card p-3"><h5>Stock Turnover</h5><p class="mb-1">Turnover Ratio</p><h3><?= number_format($stockTurnover,2) ?></h3><small>Units Sold: <?= $unitsSold ?> | Current Stock: <?= $totalStock ?></small></div></div>
  <div class="col-md-4"><div class="card p-3"><h5>Delivery Efficiency</h5><p class="mb-1">Delivered Rate</p><h3><?= number_format($deliveryEfficiency,2) ?>%</h3><small>Delivered: <?= $deliveredCount ?> / <?= $deliveryTotal ?></small></div></div>
</div>

<div class="card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Sales Performance Report</h4><span class="badge bg-success">Generated from orders</span></div>
  <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>
  <?php while($row = $salesRows->fetch_assoc()): ?>
    <tr><td>#<?= (int)$row['id'] ?></td><td><?= h($row['customer_name']) ?></td><td>Rs. <?= number_format((float)$row['total_amount'],2) ?></td><td><?= h($row['status']) ?></td><td><?= h($row['created_at']) ?></td></tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>

<div class="card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Stock Turnover Report</h4><span class="badge bg-primary">Generated from inventory and order items</span></div>
  <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Product</th><th>Category</th><th>Current Stock</th><th>Units Sold</th><th>Stock Movement</th></tr></thead><tbody>
  <?php while($row = $stockRows->fetch_assoc()): 
      $movement = ((int)$row['sold_qty'] > (int)$row['stock_count']) ? 'Fast Moving' : (((int)$row['sold_qty'] === 0) ? 'No Sales Yet' : 'Moderate Moving');
  ?>
    <tr><td><?= h($row['name']) ?></td><td><?= h($row['category']) ?></td><td><?= (int)$row['stock_count'] ?></td><td><?= (int)$row['sold_qty'] ?></td><td><?= $movement ?></td></tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>

<div class="card p-4">
  <div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Delivery Efficiency Report</h4><span class="badge bg-warning text-dark">Generated from deliveries</span></div>
  <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Delivery ID</th><th>Order ID</th><th>Driver</th><th>Route</th><th>Status</th><th>Date</th></tr></thead><tbody>
  <?php while($row = $deliveryRows->fetch_assoc()): ?>
    <tr><td>#<?= (int)$row['id'] ?></td><td>#<?= (int)$row['order_id'] ?></td><td><?= h($row['driver_name']) ?></td><td><?= h($row['route_details']) ?></td><td><?= h($row['delivery_status']) ?></td><td><?= h($row['created_at']) ?></td></tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>
<?php render_bottom(); ?>

<?php
require_once __DIR__ . '/includes/auth.php';
require_role('Logistics Officer');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)$_POST['order_id'];
    $driver = trim($_POST['driver_name']);
    $route = trim($_POST['route_details']);
    $status = trim($_POST['delivery_status']);
    $stmt = $conn->prepare('INSERT INTO deliveries(order_id, driver_name, route_details, delivery_status) VALUES(?,?,?,?)');
    $stmt->bind_param('isss', $orderId, $driver, $route, $status);
    $stmt->execute();
    $_SESSION['flash'] = 'Delivery assigned successfully.';
    header('Location: logistics.php'); exit();
}
$deliveries = $conn->query('SELECT * FROM deliveries ORDER BY id DESC');
$orders = $conn->query('SELECT id, customer_name FROM orders ORDER BY id DESC');
require_once __DIR__ . '/includes/layout.php'; render_top('Logistics Dashboard'); flash_message(); ?>
<h2 class="mb-3">Logistics Dashboard</h2>
<div class="row g-4"><div class="col-lg-4"><div class="card p-4"><h4>Assign Delivery</h4><form method="post">
<div class="mb-2"><label class="form-label">Order</label><select class="form-select" name="order_id"><?php while($o=$orders->fetch_assoc()): ?><option value="<?= (int)$o['id'] ?>">Order #<?= (int)$o['id'] ?> - <?= h($o['customer_name']) ?></option><?php endwhile; ?></select></div>
<div class="mb-2"><input class="form-control" name="driver_name" placeholder="Driver name" required></div>
<div class="mb-2"><input class="form-control" name="route_details" placeholder="Route details" required></div>
<div class="mb-3"><select class="form-select" name="delivery_status"><option>Pending</option><option>In Transit</option><option>Delivered</option></select></div>
<button class="btn btn-success w-100">Assign Delivery</button></form></div></div>
<div class="col-lg-8"><div class="card p-4"><h4>Delivery Schedule</h4><table class="table"><thead><tr><th>ID</th><th>Order</th><th>Driver</th><th>Route</th><th>Status</th></tr></thead><tbody><?php while($d=$deliveries->fetch_assoc()): ?><tr><td><?= (int)$d['id'] ?></td><td>#<?= (int)$d['order_id'] ?></td><td><?= h($d['driver_name']) ?></td><td><?= h($d['route_details']) ?></td><td><?= h($d['delivery_status']) ?></td></tr><?php endwhile; ?></tbody></table></div></div></div>
<?php render_bottom(); ?>

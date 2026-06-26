<?php
require_once __DIR__ . '/includes/auth.php';
require_role('Driver');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['delivery_id'];
    $status = $_POST['delivery_status'];
    $stmt = $conn->prepare('UPDATE deliveries SET delivery_status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $_SESSION['flash'] = 'Delivery status updated.';
    header('Location: driver.php'); exit();
}
$deliveries = $conn->query('SELECT * FROM deliveries ORDER BY id DESC');
require_once __DIR__ . '/includes/layout.php'; render_top('Driver Dashboard'); flash_message(); ?>
<h2 class="mb-3">Driver Dashboard</h2>
<div class="card p-4"><h4>My Deliveries</h4><div class="table-responsive"><table class="table"><thead><tr><th>ID</th><th>Order</th><th>Driver</th><th>Route</th><th>Status</th><th>Update</th></tr></thead><tbody><?php while($d=$deliveries->fetch_assoc()): ?><tr><td><?= (int)$d['id'] ?></td><td>#<?= (int)$d['order_id'] ?></td><td><?= h($d['driver_name']) ?></td><td><?= h($d['route_details']) ?></td><td><?= h($d['delivery_status']) ?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="delivery_id" value="<?= (int)$d['id'] ?>"><select class="form-select form-select-sm" name="delivery_status"><option <?= $d['delivery_status']==='Pending'?'selected':'' ?>>Pending</option><option <?= $d['delivery_status']==='In Transit'?'selected':'' ?>>In Transit</option><option <?= $d['delivery_status']==='Delivered'?'selected':'' ?>>Delivered</option></select><button class="btn btn-sm btn-success">Save</button></form></td></tr><?php endwhile; ?></tbody></table></div></div>
<?php render_bottom(); ?>

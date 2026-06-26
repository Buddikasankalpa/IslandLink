<?php
require_once __DIR__ . '/includes/auth.php';
require_role('RDC');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $_SESSION['flash'] = 'Order status updated.';
    header('Location: rdc_orders_manage.php'); exit();
}
$orders = $conn->query('SELECT * FROM orders ORDER BY id DESC');
require_once __DIR__ . '/includes/layout.php'; render_top('RDC Order Management'); flash_message(); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>RDC Order Management</h2><a class="btn btn-outline-secondary" href="rdc.php">Back</a></div>
<div class="card p-4"><div class="table-responsive"><table class="table"><thead><tr><th>Order ID</th><th>Customer</th><th>Address</th><th>Total</th><th>Payment</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php while($row=$orders->fetch_assoc()): ?><tr>
<td>#<?= (int)$row['id'] ?></td><td><?= h($row['customer_name']) ?></td><td><?= h($row['address']) ?></td><td>Rs. <?= number_format((float)$row['total_amount'],2) ?></td><td><?= h($row['payment_method']) ?></td><td><?= h($row['status']) ?></td>
<td><form method="post" class="d-flex gap-2"><input type="hidden" name="order_id" value="<?= (int)$row['id'] ?>"><select class="form-select form-select-sm" name="status"><option <?= $row['status']==='Pending'?'selected':'' ?>>Pending</option><option <?= $row['status']==='Processing'?'selected':'' ?>>Processing</option><option <?= $row['status']==='Completed'?'selected':'' ?>>Completed</option><option <?= $row['status']==='Cancelled'?'selected':'' ?>>Cancelled</option></select><button class="btn btn-sm btn-success">Save</button></form></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php render_bottom(); ?>

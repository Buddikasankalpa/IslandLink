<?php
require_once __DIR__ . '/includes/auth.php';
require_role('RDC');

$rdcName = 'Central RDC';

$conn->query("INSERT INTO inventory (product_id, rdc_name, stock_count)
              SELECT p.id, '$rdcName', 0
              FROM products p
              WHERE NOT EXISTS (
                  SELECT 1 FROM inventory i WHERE i.product_id = p.id AND i.rdc_name = '$rdcName'
              )");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['add_stock'])) {
    $productId = (int)$_POST['product_id'];
    $addStock = max(0, (int)$_POST['add_stock']);

    if ($addStock > 0) {
        $stmt = $conn->prepare('UPDATE inventory SET stock_count = stock_count + ? WHERE product_id = ? AND rdc_name = ?');
        $stmt->bind_param('iis', $addStock, $productId, $rdcName);
        $stmt->execute();
        $_SESSION['flash'] = 'Stock updated successfully.';
    } else {
        $_SESSION['flash'] = 'Please enter a valid stock quantity.';
    }

    header('Location: rdc.php');
    exit();
}

require_once __DIR__ . '/includes/layout.php';
render_top('RDC Dashboard');
flash_message();

$productCount = count_rows($conn, 'products');
$orderCount = count_rows($conn, 'orders');
$transferCount = count_rows($conn, 'stock_transfers');
$userCount = count_rows($conn, 'users');
$totalStock = (int)scalar($conn, "SELECT COALESCE(SUM(stock_count),0) FROM inventory WHERE rdc_name = '$rdcName'");

$inventorySql = "SELECT p.id, p.name, p.category, p.price, p.retailer, COALESCE(i.stock_count,0) AS stock_count
                 FROM products p
                 LEFT JOIN inventory i ON i.product_id = p.id AND i.rdc_name = '$rdcName'
                 ORDER BY p.id DESC";
$inventoryRows = $conn->query($inventorySql);
?>
<h2 class="mb-4">RDC Dashboard - <?= h($rdcName) ?></h2>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card p-3"><h6>Products in System</h6><h3><?= $productCount ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3"><h6>Orders to Process</h6><h3><?= $orderCount ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3"><h6>Registered Users</h6><h3><?= $userCount ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3"><h6>Current Stock Count</h6><h3><?= $totalStock ?></h3></div></div>
</div>

<div class="row g-4 dashboard-links mb-4">
  <div class="col-md-4"><a href="rdc_products.php"><div class="card p-4 h-100"><h4>Product Management</h4><p>Add new products and review existing items.</p></div></a></div>
  <div class="col-md-4"><a href="rdc_orders_manage.php"><div class="card p-4 h-100"><h4>Order Management</h4><p>View customer orders and update status.</p></div></a></div>
  <div class="col-md-4"><a href="rdc_users.php"><div class="card p-4 h-100"><h4>User Management</h4><p>View registered users and role details.</p></div></a></div>
</div>

<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Inventory Management</h4>
        <span class="badge bg-success">Live stock shown below</span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Retailer</th>
                    <th>Price</th>
                    <th>Current Stock</th>
                    <th>Add Stock</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $inventoryRows->fetch_assoc()): ?>
                <tr>
                    <td>#<?= (int)$row['id'] ?></td>
                    <td><?= h($row['name']) ?></td>
                    <td><?= h($row['category']) ?></td>
                    <td><?= h($row['retailer']) ?></td>
                    <td>Rs. <?= number_format((float)$row['price'], 2) ?></td>
                    <td>
                        <strong><?= (int)$row['stock_count'] ?></strong>
                        <?php if ((int)$row['stock_count'] <= 5): ?>
                            <span class="badge bg-danger ms-2">Low Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="product_id" value="<?= (int)$row['id'] ?>">
                            <input type="number" min="1" name="add_stock" class="form-control" placeholder="Qty" required>
                            <button class="btn btn-success btn-sm">Add</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-4">
    <h4>Recent Stock Transfers</h4>
    <?php $rows = $conn->query('SELECT * FROM stock_transfers ORDER BY id DESC LIMIT 5'); ?>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>ID</th><th>Product</th><th>From RDC</th><th>To RDC</th><th>Qty</th><th>Status</th></tr></thead>
            <tbody>
            <?php while($r = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= h($r['product_name']) ?></td>
                    <td><?= h($r['from_rdc']) ?></td>
                    <td><?= h($r['to_rdc']) ?></td>
                    <td><?= (int)$r['quantity'] ?></td>
                    <td><?= h($r['status']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_bottom(); ?>

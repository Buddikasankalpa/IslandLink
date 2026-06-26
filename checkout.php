<?php
require_once __DIR__ . '/includes/auth.php';
require_role(['Retail Customer','Admin']);
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { $_SESSION['flash'] = 'Your cart is empty.'; header('Location: cart.php'); exit(); }
$error = '';
$total = 0;
$items = [];
$ids = implode(',', array_map('intval', array_keys($cart)));
$result = $conn->query("SELECT p.*, COALESCE(i.stock_count,0) AS available_stock
                        FROM products p
                        LEFT JOIN inventory i ON i.product_id = p.id AND i.rdc_name = 'Central RDC'
                        WHERE p.id IN ($ids)");
while ($row = $result->fetch_assoc()) {
    $qty = $cart[$row['id']];
    $sub = $qty * (float)$row['price'];
    $total += $sub;
    $items[] = [$row, $qty, $sub];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Cash on Delivery');
    if ($customerName === '' || $address === '') {
        $error = 'Please fill delivery details.';
    } else {
        foreach ($items as [$p,$qty,$sub]) {
            if ((int)$p['available_stock'] < (int)$qty) {
                $error = 'Not enough stock for ' . $p['name'] . '. Available stock: ' . (int)$p['available_stock'];
                break;
            }
        }
    }

    if ($error === '') {
        $conn->begin_transaction();
        try {
            $userId = (int)current_user()['id'];
            $status = 'Pending';
            $stmt = $conn->prepare('INSERT INTO orders(user_id, customer_name, address, payment_method, total_amount, status) VALUES(?,?,?,?,?,?)');
            $stmt->bind_param('isssds', $userId, $customerName, $address, $paymentMethod, $total, $status);
            $stmt->execute();
            $orderId = $stmt->insert_id;

            $itemStmt = $conn->prepare('INSERT INTO order_items(order_id, product_id, quantity, price) VALUES(?,?,?,?)');
            $stockStmt = $conn->prepare("UPDATE inventory SET stock_count = stock_count - ? WHERE product_id = ? AND rdc_name = 'Central RDC' AND stock_count >= ?");

            foreach ($items as [$p,$qty,$sub]) {
                $price = (float)$p['price'];
                $pid = (int)$p['id'];
                $itemStmt->bind_param('iiid', $orderId, $pid, $qty, $price);
                $itemStmt->execute();

                $stockStmt->bind_param('iii', $qty, $pid, $qty);
                $stockStmt->execute();
                if ($stockStmt->affected_rows === 0) {
                    throw new Exception('Stock update failed for ' . $p['name']);
                }
            }

            $deliveryStatus = 'Pending';
            $route = 'Auto-assigned route';
            $deliveryStmt = $conn->prepare('INSERT INTO deliveries(order_id, driver_name, route_details, delivery_status) VALUES(?,?,?,?)');
            $driverName = 'Driver Team';
            $deliveryStmt->bind_param('isss', $orderId, $driverName, $route, $deliveryStatus);
            $deliveryStmt->execute();

            $conn->commit();
            unset($_SESSION['cart']);
            $_SESSION['flash'] = 'Order placed successfully. Stock updated automatically. Order ID: ' . $orderId;
            header('Location: customer.php'); exit();
        } catch (Throwable $e) {
            $conn->rollback();
            $error = 'Could not place order. ' . $e->getMessage();
        }
    }
}
require_once __DIR__ . '/includes/layout.php'; render_top('Checkout'); ?>
<h2 class="mb-3">Checkout</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<div class="row g-4"><div class="col-lg-7"><div class="card p-4">
<form method="post">
<div class="mb-3"><label class="form-label">Customer Name</label><input class="form-control" type="text" name="customer_name" value="<?= h(current_user()['name']) ?>" required></div>
<div class="mb-3"><label class="form-label">Delivery Address</label><textarea class="form-control" name="address" rows="3" required></textarea></div>
<div class="mb-3"><label class="form-label">Payment Method</label><select class="form-select" name="payment_method"><option>Cash on Delivery</option><option>Online Payment</option><option>Bank Transfer</option></select></div>
<button class="btn btn-success">Place Order</button>
</form>
</div></div>
<div class="col-lg-5"><div class="card p-4"><h4>Order Summary</h4><ul class="list-group list-group-flush mb-3">
<?php foreach ($items as [$p,$qty,$sub]): ?><li class="list-group-item d-flex justify-content-between align-items-start"><span><?= h($p['name']) ?> x <?= (int)$qty ?><br><small class="text-muted">Available stock: <?= (int)$p['available_stock'] ?></small></span><span>Rs. <?= number_format($sub,2) ?></span></li><?php endforeach; ?>
</ul><h5>Total: Rs. <?= number_format($total,2) ?></h5></div></div></div>
<?php render_bottom(); ?>

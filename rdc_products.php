<?php
require_once __DIR__ . '/includes/auth.php';
require_role('RDC');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $retailer = trim($_POST['retailer'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if ($name === '' || $price <= 0 || $retailer === '' || $category === '' || $description === '') {
        $_SESSION['flash'] = 'Please fill all product fields correctly.';
        header('Location: rdc_products.php');
        exit();
    }

    $image = 'best-product-1.jpg';
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['image']['name']));
        $image = time() . '_' . $safeName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . '/' . $image)) {
            $image = 'best-product-1.jpg';
        }
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare('INSERT INTO products(name, price, retailer, category, description, image) VALUES(?,?,?,?,?,?)');
        $stmt->bind_param('sdssss', $name, $price, $retailer, $category, $description, $image);
        if (!$stmt->execute()) {
            throw new Exception('Could not save product.');
        }
        $productId = $stmt->insert_id;

        $rdcName = 'Central RDC';
        $initialStock = 0;
        $inv = $conn->prepare('INSERT INTO inventory(product_id, rdc_name, stock_count) VALUES(?,?,?)');
        $inv->bind_param('isi', $productId, $rdcName, $initialStock);
        if (!$inv->execute()) {
            throw new Exception('Product saved but stock record could not be created.');
        }

        $conn->commit();
        $_SESSION['flash'] = 'Product added successfully. RDC can now update stock from the RDC dashboard.';
    } catch (Throwable $e) {
        $conn->rollback();
        $_SESSION['flash'] = 'Error adding product: ' . $e->getMessage();
    }

    header('Location: rdc_products.php');
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    $_SESSION['flash'] = 'Product deleted.';
    header('Location: rdc_products.php');
    exit();
}

$products = $conn->query("SELECT p.*, COALESCE(i.stock_count,0) AS stock_count
                          FROM products p
                          LEFT JOIN inventory i ON i.product_id = p.id AND i.rdc_name = 'Central RDC'
                          ORDER BY p.id DESC");
require_once __DIR__ . '/includes/layout.php';
render_top('RDC Product Management');
flash_message();
?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>RDC Product Management</h2><a class="btn btn-outline-secondary" href="rdc.php">Back</a></div>
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card p-4">
      <h4>Add Product</h4>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="add_product" value="1">
        <div class="mb-2"><input class="form-control" name="name" placeholder="Product name" required></div>
        <div class="mb-2"><input class="form-control" type="number" step="0.01" min="0.01" name="price" placeholder="Price" required></div>
        <div class="mb-2"><input class="form-control" name="retailer" placeholder="Retailer" required></div>
        <div class="mb-2">
          <select class="form-select" name="category" required>
            <option value="">Select category</option>
            <option>Fruits</option>
            <option>Vegetables</option>
            <option>Packaged Food</option>
            <option>Household Items</option>
            <option>Beverages</option>
            <option>Personal Care</option>
          </select>
        </div>
        <div class="mb-2"><textarea class="form-control" name="description" rows="3" placeholder="Description" required></textarea></div>
        <div class="alert alert-info py-2">New products are created with <strong>0 stock</strong>. Use the RDC dashboard inventory section to add stock.</div>
        <div class="mb-3"><input class="form-control" type="file" name="image" accept="image/*"></div>
        <button class="btn btn-success w-100" type="submit">Add Product</button>
      </form>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card p-4">
      <h4>Product List</h4>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Retailer</th><th>Stock</th><th></th></tr></thead>
          <tbody>
          <?php while($row=$products->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$row['id'] ?></td>
              <td><?= h($row['name']) ?></td>
              <td><?= h($row['category']) ?></td>
              <td>Rs. <?= number_format((float)$row['price'],2) ?></td>
              <td><?= h($row['retailer']) ?></td>
              <td><?= (int)$row['stock_count'] ?></td>
              <td><a class="btn btn-sm btn-danger" href="?delete=<?= (int)$row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php render_bottom(); ?>

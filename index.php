<?php require_once __DIR__ . '/includes/layout.php'; render_top('ISDN Home'); ?>
<div class="hero">
  <div class="row align-items-center g-4">
    <div class="col-lg-7">
      <h1 class="display-5 fw-bold text-success">Centralised Sales Distribution Management System</h1>
      <p class="lead">IslandLink connects retail customers, RDC staff, Head Office, drivers and logistics teams in one system for ordering, inventory, delivery scheduling and reporting.</p>
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-success" href="shop.php">Browse Products</a>
        <a class="btn btn-outline-success" href="login.php">System Login</a>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card p-3">
        <img src="img/Online-Retail-Store.jpg" alt="ISDN" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>
<div class="row g-4 mt-2">
  <div class="col-md-4"><div class="card p-4 h-100"><h4>Order Management</h4><p>Retail customers can view products, add items to cart and place orders online.</p></div></div>
  <div class="col-md-4"><div class="card p-4 h-100"><h4>Inventory Synchronisation</h4><p>RDC and Head Office users can monitor product levels and stock movements from one system.</p></div></div>
  <div class="col-md-4"><div class="card p-4 h-100"><h4>Delivery Tracking</h4><p>Logistics staff and drivers can manage delivery status, routes and order fulfilment.</p></div></div>
</div>
<?php render_bottom(); ?>

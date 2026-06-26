<?php
require_once __DIR__ . '/auth.php';

function render_top(string $title) {
    $user = current_user();
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . h($title) . '</title>';
    echo '<link rel="stylesheet" href="css/bootstrap.min.css">';
    echo '<link rel="stylesheet" href="assets/css/bootstrap.min.css">';
    echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">';
    echo '<style>body{background:#f4f6f9}.topbar{background:#198754}.card{border:none;box-shadow:0 2px 12px rgba(0,0,0,.08)}.hero{padding:90px 0 40px}.navbar-brand{font-weight:700}.table td,.table th{vertical-align:middle}.dashboard-links a{text-decoration:none}.product-img{height:180px;object-fit:cover}.badge-role{font-size:.85rem}</style>';
    echo '</head><body>';
    echo '<nav class="navbar navbar-expand-lg navbar-dark topbar"><div class="container">';
    echo '<a class="navbar-brand" href="index.php">IslandLink ISDN</a>';
    echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>';
    echo '<div class="collapse navbar-collapse" id="nav"><ul class="navbar-nav ms-auto align-items-lg-center">';
    echo '<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>';
    echo '<li class="nav-item"><a class="nav-link" href="shop.php">Products</a></li>';
    if ($user) {
        echo '<li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>';
        echo '<li class="nav-item"><span class="nav-link">' . h($user['name']) . ' <span class="badge bg-light text-dark badge-role">' . h($user['role']) . '</span></span></li>';
        echo '<li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="logout.php">Logout</a></li>';
    } else {
        echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
        echo '<li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="register.php">Register</a></li>';
    }
    echo '</ul></div></div></nav><div class="container py-4">';
}

function render_bottom() {
    echo '</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>';
}

function flash_message() {
    if (!empty($_SESSION['flash'])) {
        echo '<div class="alert alert-info">' . h($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}
?>

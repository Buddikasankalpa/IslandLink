<?php
require 'db.php'; // same folder

$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="uploads/'.$row['image'].'" class="card-img-top" alt="'.$row['name'].'">
                <div class="card-body">
                    <h5 class="card-title">'.$row['name'].'</h5>
                    <p class="card-text">'.$row['description'].'</p>
                    <p class="card-text fw-bold">Rs. '.$row['price'].'</p>
                    <a href="#" class="btn btn-primary w-100">Add to Cart</a>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<p class="text-muted">No products found.</p>';
}
?>
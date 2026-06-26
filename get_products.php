<?php
require 'db.php';

$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo '
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="rounded position-relative fruite-item">
                <div class="fruite-img">
                    <img src="uploads/'.$row['image'].'" class="img-fluid w-100 rounded-top" alt="'.$row['name'].'">
                </div>
                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">'.$row['category'].'</div>
                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                    <h4>'.$row['name'].'</h4>
                    <p>'.$row['description'].'</p>
                    <div class="d-flex justify-content-between flex-lg-wrap">
                        <p class="text-dark fs-5 fw-bold mb-0">Rs.'.$row['price'].'</p>
                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<p class="text-muted">No products found.</p>';
}
?>
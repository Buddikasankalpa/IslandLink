<?php
require_once __DIR__ . '/includes/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'Retail Customer';
    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill all fields.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users(name, email, password, role) VALUES(?,?,?,?)');
            $stmt->bind_param('ssss', $name, $email, $hash, $role);
            $stmt->execute();
            $_SESSION['flash'] = 'Registration successful. Please login.';
            header('Location: login.php');
            exit();
        }
    }
}
require_once __DIR__ . '/includes/layout.php'; render_top('Register'); ?>
<div class="row justify-content-center"><div class="col-md-7 col-lg-5"><div class="card p-4">
<h3 class="mb-3 text-center">Create Account</h3>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">Full Name</label><input class="form-control" type="text" name="name" required></div>
  <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
  <div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
  <div class="mb-3"><label class="form-label">Role</label>
    <select class="form-select" name="role">
      <option>Retail Customer</option><option>RDC</option><option>Head Office</option><option>Driver</option><option>Logistics Officer</option>
    </select>
  </div>
  <button class="btn btn-success w-100" type="submit">Register</button>
</form>
</div></div></div>
<?php render_bottom(); ?>

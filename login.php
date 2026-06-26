<?php
require_once __DIR__ . '/includes/auth.php';
if (current_user()) { redirect_by_role(current_user()['role']); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        $_SESSION['flash'] = 'Login successful.';
        redirect_by_role($user['role']);
    }
    $error = 'Invalid email or password.';
}
require_once __DIR__ . '/includes/layout.php';
render_top('Login');
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-4"><div class="card p-4">
<h3 class="mb-3 text-center">ISDN Login</h3>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
  <div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
  <button class="btn btn-success w-100" type="submit">Login</button>
</form>
<p class="mt-3 mb-0 text-center">No account? <a href="register.php">Register here</a></p>
<div class="mt-3 small text-muted">
  Demo emails: admin@gmail.com, customer@gmail.com, rdc@gmail.com, homanager@gmail.com, logisticsofficer@gmail.com, driver@gmail.com<br>
  Demo password for seeded users: <strong>123456</strong>
</div>
</div></div></div>
<?php render_bottom(); ?>

<?php
require_once __DIR__ . '/includes/auth.php';
require_role('RDC');
$users = $conn->query('SELECT id,name,email,role,created_at FROM users ORDER BY id DESC');
require_once __DIR__ . '/includes/layout.php'; render_top('RDC User Management'); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h2>RDC User Management</h2><a class="btn btn-outline-secondary" href="rdc.php">Back</a></div>
<div class="card p-4"><div class="table-responsive"><table class="table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead><tbody>
<?php while($row=$users->fetch_assoc()): ?><tr><td><?= (int)$row['id'] ?></td><td><?= h($row['name']) ?></td><td><?= h($row['email']) ?></td><td><?= h($row['role']) ?></td><td><?= h($row['created_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php render_bottom(); ?>

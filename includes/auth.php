<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../db.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: login.php');
        exit();
    }
}

function require_role($roles) {
    require_login();
    $roles = (array)$roles;
    if (!in_array(current_user()['role'], $roles, true)) {
        header('Location: dashboard.php');
        exit();
    }
}

function redirect_by_role($role) {
    switch ($role) {
        case 'Admin': header('Location: admin.php'); break;
        case 'RDC': header('Location: rdc.php'); break;
        case 'Head Office': header('Location: ho.php'); break;
        case 'Retail Customer': header('Location: customer.php'); break;
        case 'Driver': header('Location: driver.php'); break;
        case 'Logistics Officer': header('Location: logistics.php'); break;
        default: header('Location: index.php');
    }
    exit();
}

function count_rows(mysqli $conn, string $table): int {
    $result = $conn->query("SELECT COUNT(*) AS total FROM {$table}");
    $row = $result ? $result->fetch_assoc() : ['total' => 0];
    return (int)($row['total'] ?? 0);
}

function scalar(mysqli $conn, string $sql) {
    $result = $conn->query($sql);
    if (!$result) return 0;
    $row = $result->fetch_row();
    return $row[0] ?? 0;
}
?>

<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_username'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Kiểm tra xem cột viewed_by_admin có tồn tại không
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'viewed_by_admin'");
if (mysqli_num_rows($check_column) > 0) {
    $sql = "SELECT COUNT(*) as new_orders FROM orders WHERE status = 'pending' AND viewed_by_admin = 0";
} else {
    $sql = "SELECT COUNT(*) as new_orders FROM orders WHERE status = 'pending'";
}

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

echo json_encode($data);
?>
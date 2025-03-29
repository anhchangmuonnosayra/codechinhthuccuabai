<?php
session_start();
include 'db_connect.php';

// Kiểm tra quyền admin (đồng bộ với admin_add_product.php)
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Kiểm tra kết nối cơ sở dữ liệu
if ($conn->connect_error) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $conn->connect_error);
}

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id == 0) {
    $_SESSION['error'] = "ID sản phẩm không hợp lệ.";
    header("Location: admin_add_product.php");
    exit();
}

// Lấy thông tin sản phẩm để xóa ảnh
$stmt = $conn->prepare("SELECT image_url FROM product WHERE id = ?");
if (!$stmt) {
    $_SESSION['error'] = "Lỗi chuẩn bị truy vấn: " . $conn->error;
    header("Location: admin_add_product.php");
    exit();
}
$stmt->bind_param("i", $product_id);
if (!$stmt->execute()) {
    $_SESSION['error'] = "Lỗi thực thi truy vấn: " . $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: admin_add_product.php");
    exit();
}
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// Kiểm tra xem sản phẩm có tồn tại không
if ($product) {
    // Xóa ảnh từ server nếu tồn tại
    if (!empty($product['image_url']) && file_exists($product['image_url'])) {
        if (!unlink($product['image_url'])) {
            $_SESSION['warning'] = "Không thể xóa file ảnh: " . $product['image_url'];
        }
    } elseif (!empty($product['image_url'])) {
        $_SESSION['warning'] = "File ảnh không tồn tại trên server: " . $product['image_url'];
    }

    // Xóa sản phẩm từ database
    $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Lỗi chuẩn bị truy vấn xóa: " . $conn->error;
        $conn->close();
        header("Location: admin_add_product.php");
        exit();
    }
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Xóa sản phẩm thành công!";
        } else {
            $_SESSION['error'] = "Không tìm thấy sản phẩm để xóa.";
        }
    } else {
        $_SESSION['error'] = "Lỗi khi xóa sản phẩm: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Không tìm thấy sản phẩm với ID: $product_id.";
}

$conn->close();
header("Location: admin_add_product.php");
exit();
?>
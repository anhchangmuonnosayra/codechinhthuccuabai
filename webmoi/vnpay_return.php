<?php
session_start();
include 'db_connect.php';
include 'config.php';

// Kiểm tra dữ liệu trả về từ VNPAY
$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = http_build_query($inputData);
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

if ($secureHash === $vnp_SecureHash && isset($_GET['vnp_ResponseCode'])) {
    $order_id = explode("_", $_GET['vnp_TxnRef'])[0]; // Lấy order_id từ vnp_TxnRef
    $vnpay_txn_ref = $_GET['vnp_TxnRef'];
    $status = $_GET['vnp_ResponseCode'] == '00' ? 'completed' : 'failed';

    // Cập nhật trạng thái đơn hàng
    $sql = "UPDATE orders SET status = '$status', vnpay_txn_ref = '$vnpay_txn_ref' WHERE id = $order_id";
    mysqli_query($conn, $sql);

    if ($status == 'completed') {
        // Xóa sản phẩm đã thanh toán khỏi giỏ hàng
        $checkout_items = $_SESSION['checkout_items'] ?? [];
        $cart = json_decode($_SESSION['cart'] ?? '[]', true);
        $remaining_cart = array_filter($cart, function($item) use ($checkout_items) {
            return !in_array($item, $checkout_items);
        });
        echo '<script>localStorage.setItem("cart", JSON.stringify(' . json_encode(array_values($remaining_cart)) . '));</script>';
        unset($_SESSION['checkout_items']);
        unset($_SESSION['order_id']);
        unset($_SESSION['total']);
        header("Location: thanhtoan.php?success=1");
    } else {
        header("Location: thanhtoan.php?error=Thanh toán thất bại: " . $_GET['vnp_ResponseCode']);
    }
} else {
    header("Location: thanhtoan.php?error=Chữ ký không hợp lệ");
}

mysqli_close($conn);
exit();
?>
<?php
session_start();
include 'config.php'; // File cấu hình chứa vnp_TmnCode, vnp_HashSecret, vnp_Url

if (!isset($_SESSION['order_id']) || !isset($_SESSION['total'])) {
    header("Location: thanhtoan.php?error=Không tìm thấy thông tin đơn hàng");
    exit();
}

$vnp_TxnRef = $_SESSION['order_id'] . "_" . time(); // Mã giao dịch duy nhất
$vnp_OrderInfo = "Thanh toán đơn hàng #" . $_SESSION['order_id'];
$vnp_OrderType = 'billpayment';
$vnp_Amount = $_SESSION['total'] * 100; // Đơn vị: cent (x100)
$vnp_Locale = 'vn';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_ReturnUrl,
    "vnp_TxnRef" => $vnp_TxnRef,
);

ksort($inputData);
$query = http_build_query($inputData);
$hashdata = $query;
$vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
$vnp_Url .= '?' . $query . "&vnp_SecureHash=" . $vnp_SecureHash;

header("Location: $vnp_Url");
exit();
?>
<?php
include 'config.php';
include 'db_connect.php';

$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}
$vnp_SecureHash = $_GET['vnp_SecureHash'];
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = http_build_query($inputData);
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

if ($secureHash === $vnp_SecureHash) {
    $order_id = explode("_", $_GET['vnp_TxnRef'])[0];
    $status = $_GET['vnp_ResponseCode'] == '00' ? 'completed' : 'failed';
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    mysqli_query($conn, $sql);
    echo "SUCCESS";
} else {
    echo "INVALID SIGNATURE";
}

mysqli_close($conn);
?>
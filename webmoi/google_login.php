<?php
session_start();
require_once 'db_connect.php';

// Xác thực token với Google
require_once 'vendor/autoload.php'; // Cần cài đặt Google API Client

$client = new Google_Client(['client_id' => '398276343567-sq76i5iao5tvct8k3lcdoubkamd8ichm.apps.googleusercontent.com']);
$payload = $client->verifyIdToken($_POST['id_token']);

if ($payload) {
    $email = $payload['email'];
    $name = $payload['name'];
    $google_id = $payload['sub'];
    
    // Kiểm tra user đã tồn tại chưa
    $sql = "SELECT * FROM users WHERE email = '$email' OR google_id = '$google_id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Đăng nhập
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
    } else {
        // Đăng ký mới
        $sql = "INSERT INTO users (username, email, google_id) VALUES ('$name', '$email', '$google_id')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['username'] = $name;
        }
    }
    
    header("Location: index.php");
    exit();
} else {
    die("Lỗi xác thực Google");
}
?>
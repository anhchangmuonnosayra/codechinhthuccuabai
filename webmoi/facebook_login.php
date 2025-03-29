<?php
session_start();
require_once 'db_connect.php';

// Lấy token từ Facebook
$access_token = $_GET['token'];

// Gọi API Facebook để lấy thông tin người dùng
$url = "https://graph.facebook.com/v12.0/me?fields=id,name,email&access_token=$access_token";
$response = file_get_contents($url);
$user = json_decode($response, true);

if (isset($user['email'])) {
    // Kiểm tra email đã tồn tại chưa
    $email = $user['email'];
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Đăng nhập
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
    } else {
        // Đăng ký mới
        $username = $user['name'];
        $sql = "INSERT INTO users (username, email, facebook_id) VALUES ('$username', '$email', '{$user['id']}')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['username'] = $username;
        }
    }
    
    header("Location: index.php");
    exit();
} else {
    die("Lỗi đăng nhập Facebook");
}
?>
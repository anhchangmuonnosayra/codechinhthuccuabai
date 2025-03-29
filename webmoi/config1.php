<?php
// Khởi tạo session
session_start();

// Cấu hình cơ bản
define('BASE_URL', 'http://localhost/webmoi/'); // Đường dẫn cơ sở (thay đổi khi deploy)
define('APP_NAME', '3BROPACK');

// Cấu hình kết nối database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mật khẩu MySQL (rỗng nếu dùng XAMPP mặc định)
define('DB_NAME', 'webmoi'); // Tên cơ sở dữ liệu

// Hàm kết nối database
function connectDB() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }
    return $conn;
}

// Hàm đóng kết nối
function closeDB($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
}

// Khởi tạo kết nối toàn cục (nếu cần)
$conn = connectDB();
?>

<?php
// Ngăn chặn truy cập trực tiếp file config
if (!defined('BASE_URL')) {
    die('Truy cập không hợp lệ!');
}
?>
<?php
session_start();
include 'db_connect.php';

// Bật chế độ hiển thị lỗi để gỡ lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra kết nối database
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}

// Kiểm tra nếu admin đã đăng nhập
if (isset($_SESSION['admin_username'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Xử lý đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Kiểm tra xem username có tồn tại không
    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $error = "Lỗi truy vấn SQL: " . mysqli_error($conn);
    } elseif (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        // Kiểm tra mật khẩu
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Mật khẩu không đúng.";
        }
    } else {
        $error = "Tên đăng nhập không tồn tại.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Admin - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6">Đăng Nhập Admin</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm mb-2" for="username">Tên Đăng Nhập</label>
                <input class="w-full px-3 py-2 border rounded" type="text" id="username" name="username" value="admin12" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-2" for="password">Mật Khẩu</label>
                <input class="w-full px-3 py-2 border rounded" type="password" id="password" name="password" value="admin123" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>
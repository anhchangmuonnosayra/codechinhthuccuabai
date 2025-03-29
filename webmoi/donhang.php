<?php
include 'navbar.php';
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quanlytaikhoan";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT ten_nguoi_dung, email, dia_chi, so_dien_thoai FROM nguoidung WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ten_nguoi_dung = $row['ten_nguoi_dung'];
    $email = $row['email'];
    $dia_chi = $row['dia_chi'];
    $so_dien_thoai = $row['so_dien_thoai'];
} else {
    echo "Không tìm thấy thông tin người dùng.";
    exit();
}

// Xử lý cập nhật thông tin tài khoản
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (Mã cập nhật thông tin tài khoản) ...
}

// Lấy lịch sử đơn hàng
$sql_donhang = "SELECT * FROM donhang WHERE user_id = ?";
$stmt_donhang = $conn->prepare($sql_donhang);
$stmt_donhang->bind_param("i", $user_id);
$stmt_donhang->execute();
$result_donhang = $stmt_donhang->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Tài Khoản</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f7fa;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
        }

        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .order-list {
            margin-top: 20px;
        }

        .order-list table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        .order-list th, .order-list td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .order-list th {
            background-color: #3498db;
            color: #fff;
            font-weight: bold;
        }

        .order-list td {
            color: #555;
        }

        .order-list tr:hover {
            background-color: #f9f9f9;
        }

        .no-orders {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-style: italic;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 24px;
            }

            h2 {
                font-size: 18px;
            }

            .order-list th, .order-list td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thông Tin Tài Khoản</h1>

        <!-- Phần form nếu cần -->
        <form method="post" action="taikhoan.php">
            <!-- Thêm các trường thông tin nếu cần -->
        </form>

        <div class="order-list">
            <h2>Lịch Sử Đơn Hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th>Mã Đơn Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_donhang->num_rows > 0) {
                        while ($row_donhang = $result_donhang->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row_donhang['id'] . "</td>";
                            echo "<td>" . $row_donhang['ngay_dat'] . "</td>";
                            echo "<td>" . $row_donhang['tong_tien'] . "</td>";
                            echo "<td>" . $row_donhang['trang_thai'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='no-orders'>Không có đơn hàng nào.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
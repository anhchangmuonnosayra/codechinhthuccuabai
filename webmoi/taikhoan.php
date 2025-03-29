<?php
session_start();
include 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Lấy thông tin người dùng
$sql = "SELECT username, email, full_name, phone, address, password, total_spent, membership_rank FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Tính tổng chi tiêu từ bảng orders
$sql_spent = "SELECT SUM(total) as total_spent FROM orders WHERE username = '$username' AND status = 'completed'";
$result_spent = mysqli_query($conn, $sql_spent);
if (!$result_spent) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
$spent_data = mysqli_fetch_assoc($result_spent);
$total_spent = $spent_data['total_spent'] ?? 0.00;

// Xác định rank thành viên
if ($total_spent >= 5000000) {
    $membership_rank = 'Vàng';
    $rank_color = 'color: #FFD700;';
    $rank_bg = 'background-color: #FFF9E6;';
} elseif ($total_spent >= 1000000) {
    $membership_rank = 'Bạc';
    $rank_color = 'color: #C0C0C0;';
    $rank_bg = 'background-color: #F5F5F5;';
} else {
    $membership_rank = 'Đồng';
    $rank_color = 'color: #CD7F32;';
    $rank_bg = 'background-color: #F8F1E8;';
}

// Cập nhật total_spent và membership_rank
$sql_update_rank = "UPDATE users SET total_spent = '$total_spent', membership_rank = '$membership_rank' WHERE username = '$username'";
mysqli_query($conn, $sql_update_rank);

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_address = mysqli_real_escape_string($conn, $_POST['address']);
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    $sql = "UPDATE users SET 
            username = '$new_username', 
            email = '$new_email', 
            full_name = '$new_full_name', 
            phone = '$new_phone', 
            address = '$new_address'";
    if (!empty($_POST['password'])) {
        $sql .= ", password = '$new_password'";
    }
    $sql .= " WHERE username = '$username'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['username'] = $new_username;
        $update_success = "Cập nhật thông tin thành công!";
        $sql = "SELECT username, email, full_name, phone, address, password, total_spent, membership_rank FROM users WHERE username = '$new_username'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
    } else {
        $update_error = "Lỗi: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản Người Dùng | 3BROPACK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
</head>
<body style="font-family: 'Inter', sans-serif; background-color: #f5f5f5; color: #333; margin: 0; padding: 0;">
    <?php include 'navbar.php'; ?>


    <div style="max-width: 1200px; margin: 24px auto; padding: 0 16px;">
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Sidebar -->
            <div style="width: 100%;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); margin-bottom: 16px;">
                    <div style="padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #f0f0f0;">
                        <img src="https://via.placeholder.com/80" alt="User Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #ee4d2d;">
                        <div>
                            <div style="font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div style="font-size: 13px; color: #666; margin-top: 4px;">Thành viên 
                                <span style="<?php echo $rank_color; ?> <?php echo $rank_bg; ?> padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 4px;">
                                    <?php echo $membership_rank; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="taikhoan.php" style="display: block; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #ee4d2d; background-color: #fff8f7; border-left: 3px solid #ee4d2d; transition: all 0.2s;">
                        <i class="fas fa-user-circle" style="margin-right: 8px;"></i> Thông tin tài khoản
                    </a>
                    <a href="user_orders.php" style="display: block; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #333; transition: all 0.2s;">
                        <i class="fas fa-clipboard-list" style="margin-right: 8px;"></i> Đơn hàng của tôi
                    </a>

                </div>
                
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 16px;">
                    <div style="font-size: 14px; font-weight: 500; margin-bottom: 8px;">Tổng chi tiêu</div>
                    <div style="font-size: 24px; font-weight: 700; color: #ee4d2d; margin-bottom: 8px;">
                        <?php echo number_format($total_spent, 0, ',', '.'); ?>₫
                    </div>
                    <div style="font-size: 12px; color: #666;">
                        <?php 
                        if ($membership_rank == 'Vàng') {
                            echo "Bạn đang là thành viên Vàng";
                        } elseif ($membership_rank == 'Bạc') {
                            echo "Cần thêm ".number_format(5000000 - $total_spent, 0, ',', '.')."₫ để lên hạng Vàng";
                        } else {
                            echo "Cần thêm ".number_format(1000000 - $total_spent, 0, ',', '.')."₫ để lên hạng Bạc";
                        }
                        ?>
                    </div>
                </div>
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 24px;">
                    <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">Thông tin thành viên</h2>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="<?php echo $rank_color; ?> font-size: 32px;">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500;">Hạng thành viên: 
                                <span style="<?php 
                                    if ($membership_rank == 'Vàng') echo 'background: linear-gradient(135deg, #FFD700, #FFA500); color: #7a4a00;';
                                    elseif ($membership_rank == 'Bạc') echo 'background: linear-gradient(135deg, #C0C0C0, #A8A8A8); color: #4a4a4a;';
                                    else echo 'background: linear-gradient(135deg, #CD7F32, #A67C52); color: #fff;';
                                ?> padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-left: 8px;">
                                    <?php echo $membership_rank; ?>
                                </span>
                            </div>
                            <div style="font-size: 13px; color: #666; margin-top: 8px;">
                                <?php 
                                if ($membership_rank == 'Vàng') {
                                    echo "Bạn đang ở hạng cao nhất!";
                                } elseif ($membership_rank == 'Bạc') {
                                    echo "Cần thêm ".number_format(5000000 - $total_spent, 0, ',', '.')."₫ để lên hạng Vàng";
                                } else {
                                    echo "Cần thêm ".number_format(1000000 - $total_spent, 0, ',', '.')."₫ để lên hạng Bạc";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div style="width: 100%;">
                <div style="background: white; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 24px; margin-bottom: 24px;">
                    <h1 style="font-size: 20px; font-weight: 700; margin-bottom: 24px;">Thông tin tài khoản</h1>
                    
                    <?php if (isset($update_success)): ?>
                        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                            <?php echo $update_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($update_error)): ?>
                        <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                            <?php echo $update_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" style="display: grid; gap: 16px;">
                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                <div>
                                    <label style="display: block; color: #555; font-weight: 500; margin-bottom: 6px;">Tên người dùng</label>
                                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required 
                                           style="border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; width: 100%; transition: all 0.2s;">
                                </div>
                                <div>
                                    <label style="display: block; color: #555; font-weight: 500; margin-bottom: 6px;">Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required 
                                           style="border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; width: 100%; transition: all 0.2s;">
                                </div>
                            </div>
                            
                            <div>
                                <label style="display: block; color: #555; font-weight: 500; margin-bottom: 6px;">Họ và tên</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" 
                                       style="border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; width: 100%; transition: all 0.2s;">
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                <div>
                                    <label style="display: block; color: #555; font-weight: 500; margin-bottom: 6px;">Số điện thoại</label>
                                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" pattern="[0-9]{10}" title="Số điện thoại phải có 10 chữ số" 
                                           style="border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; width: 100%; transition: all 0.2s;">
                                </div>
                                <div>
                                    <label style="display: block; color: #555; font-weight: 500; margin-bottom: 6px;">Mật khẩu mới</label>
                                    <input type="password" name="password" placeholder="Để trống nếu không đổi" 
                                           style="border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; width: 100%; transition: all 0.2s;">
                                </div>
                            </div>
                            
                            <div>
                                <label style="display: block; color: #555; font-weight: 500; margin-bottom: 6px;">Địa chỉ</label>
                                <textarea name="address" rows="3" style="border: 1px solid #ddd; border-radius: 4px; padding: 10px 12px; width: 100%; transition: all 0.2s;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div style="padding-top: 16px;">
                                <button type="submit" style="background-color: #ee4d2d; color: white; padding: 10px 24px; border-radius: 4px; font-weight: 500; transition: all 0.2s; border: none; cursor: pointer;">
                                    Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
    <?php mysqli_close($conn); ?>
</body>
</html>
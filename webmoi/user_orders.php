<?php
session_start();
include 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Xử lý hủy đơn hàng
if (isset($_POST['cancel_order'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $sql_cancel = "UPDATE orders SET status = 'cancelled' WHERE id = '$order_id' AND username = '$username' AND status = 'pending'";
    if (mysqli_query($conn, $sql_cancel) && mysqli_affected_rows($conn) > 0) {
        $cancel_message = "Đơn hàng #$order_id đã được hủy thành công!";
    } else {
        $cancel_error = "Không thể hủy đơn hàng #$order_id. Đơn hàng có thể đã được xử lý.";
    }
}

// Xử lý cập nhật địa chỉ giao hàng
if (isset($_POST['update_address'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_address = mysqli_real_escape_string($conn, $_POST['new_address']);
    $sql_update = "UPDATE orders SET delivery_address = '$new_address' WHERE id = '$order_id' AND username = '$username' AND status = 'pending'";
    if (mysqli_query($conn, $sql_update) && mysqli_affected_rows($conn) > 0) {
        $update_message = "Địa chỉ giao hàng của đơn hàng #$order_id đã được cập nhật!";
    } else {
        $update_error = "Không thể cập nhật địa chỉ đơn hàng #$order_id.";
    }
}

// Lấy danh sách đơn hàng của người dùng
$sql_orders = "SELECT id, total, delivery_address, payment_method, status, created_at 
               FROM orders 
               WHERE username = '$username' 
               ORDER BY created_at DESC";
$result_orders = mysqli_query($conn, $sql_orders);

if (!$result_orders) {
    die("Lỗi truy vấn SQL: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng | 3BROPACK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <script>
        function confirmCancel(orderId) {
            if (confirm("Bạn có chắc chắn muốn hủy đơn hàng #" + orderId + " không?")) {
                document.getElementById('cancel_form_' + orderId).submit();
            }
        }

        function showEditAddress(orderId, currentAddress) {
            document.getElementById('edit_address_' + orderId).style.display = 'block';
            document.getElementById('new_address_' + orderId).value = currentAddress;
        }
    </script>
</head>
<body style="font-family: 'Inter', sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
    <?php include 'navbar.php'; ?>


    <div style="max-width: 1200px; margin: 16px auto; padding: 0 16px;">
        <div style="display: flex; gap: 16px;">
            <!-- Sidebar -->
            <div style="width: 250px; flex-shrink: 0;">
                <div style="background: white; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,0.05); margin-bottom: 16px;">
                    <div style="padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #f0f0f0;">
                        <img src="https://via.placeholder.com/60" alt="User Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #ee4d2d;">
                        <div>
                            <div style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <div style="font-size: 13px; color: #666;">Tài khoản của tôi</div>
                        </div>
                    </div>
                    <a href="taikhoan.php" style="display: block; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #333;">
                        <i class="fas fa-user-circle" style="margin-right: 8px;"></i> Thông tin tài khoản
                    </a>
                    <a href="user_orders.php" style="display: block; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #ee4d2d; background-color: #fff8f7; border-left: 3px solid #ee4d2d;">
                        <i class="fas fa-clipboard-list" style="margin-right: 8px;"></i> Đơn hàng của tôi
                    </a>

                </div>
            </div>
            
            <!-- Main Content -->
            <div style="flex-grow: 1;">
                <div style="background: white; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,0.05); padding: 16px;">
                    <h1 style="font-size: 20px; font-weight: 700; margin-bottom: 16px;">Quản lý đơn hàng</h1>
                    
                    <?php if (isset($cancel_message)): ?>
                        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                            <?php echo $cancel_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($cancel_error)): ?>
                        <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                            <?php echo $cancel_error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($update_message)): ?>
                        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                            <?php echo $update_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($update_error)): ?>
                        <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                            <?php echo $update_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (mysqli_num_rows($result_orders) > 0): ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead style="background-color: #f5f5f5;">
                                    <tr>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Mã đơn hàng</th>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Tổng tiền</th>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Địa chỉ giao</th>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Thanh toán</th>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Trạng thái</th>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Ngày đặt</th>
                                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #333;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = mysqli_fetch_assoc($result_orders)): ?>
                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                            <td style="padding: 16px; vertical-align: top;">#<?php echo htmlspecialchars($order['id']); ?></td>
                                            <td style="padding: 16px; vertical-align: top;"><?php echo number_format($order['total'], 0, ',', '.'); ?>₫</td>
                                            <td style="padding: 16px; vertical-align: top;"><?php echo htmlspecialchars($order['delivery_address']); ?></td>
                                            <td style="padding: 16px; vertical-align: top;"><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                            <td style="padding: 16px; vertical-align: top;">
                                                <span style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 13px; 
                                                    <?php 
                                                        switch($order['status']) {
                                                            case 'pending': echo 'background-color: #fff4e5; color: #ff9500;'; break;
                                                            case 'shipped': echo 'background-color: #e6f3ff; color: #007bff;'; break;
                                                            case 'completed': echo 'background-color: #e8f8f0; color: #28a745;'; break;
                                                            case 'cancelled': echo 'background-color: #fee; color: #dc3545;'; break;
                                                            default: echo 'background-color: #f0f0f0; color: #6c757d;';
                                                        }
                                                    ?>">
                                                    <?php 
                                                        switch($order['status']) {
                                                            case 'pending': echo 'Đang xử lý'; break;
                                                            case 'shipped': echo 'Đang giao hàng'; break;
                                                            case 'completed': echo 'Hoàn thành'; break;
                                                            case 'cancelled': echo 'Đã hủy'; break;
                                                            default: echo 'Không xác định';
                                                        }
                                                    ?>
                                                </span>
                                            </td>
                                            <td style="padding: 16px; vertical-align: top;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td style="padding: 16px; vertical-align: top;">
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <button onclick="confirmCancel(<?php echo $order['id']; ?>)" style="background-color: #fff; border: 1px solid #dc3545; color: #dc3545; padding: 6px 12px; border-radius: 4px; font-size: 13px; cursor: pointer; margin-right: 8px;">Hủy đơn</button>
                                                    <form id="cancel_form_<?php echo $order['id']; ?>" method="POST" style="display: none;">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <input type="hidden" name="cancel_order" value="1">
                                                    </form>
                                                    <button onclick="showEditAddress(<?php echo $order['id']; ?>, '<?php echo addslashes($order['delivery_address']); ?>')" style="background-color: #fff; border: 1px solid #007bff; color: #007bff; padding: 6px 12px; border-radius: 4px; font-size: 13px; cursor: pointer;">Sửa địa chỉ</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <!-- Form sửa địa chỉ (ẩn mặc định) -->
                                        <tr id="edit_address_<?php echo $order['id']; ?>" style="display: none; background-color: #f9f9f9;">
                                            <td colspan="7" style="padding: 16px;">
                                                <form method="POST" style="display: flex; gap: 8px;">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <textarea name="new_address" id="new_address_<?php echo $order['id']; ?>" style="flex-grow: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; min-height: 60px;"><?php echo htmlspecialchars($order['delivery_address']); ?></textarea>
                                                    <button type="submit" name="update_address" style="background-color: #ee4d2d; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Lưu</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px 0;">
                            <img src="https://laz-img-cdn.alicdn.com/tfs/TB1vYlkdnZmx1VjSZFGXXax2XXa-72-72.png" alt="No orders" style="width: 72px; height: 72px; margin-bottom: 16px;">
                            <div style="font-size: 16px; color: #333; margin-bottom: 8px;">Bạn chưa có đơn hàng nào</div>
                            <a href="/" style="display: inline-block; background-color: #ee4d2d; color: white; padding: 10px 24px; border-radius: 4px; text-decoration: none; font-weight: 500;">Mua sắm ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
    <?php mysqli_close($conn); ?>
</body>
</html>
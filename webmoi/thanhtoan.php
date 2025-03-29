<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$sql_user = "SELECT full_name, phone, address FROM users WHERE username = '$username'";
$result_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($result_user);

$checkout_items = $_SESSION['checkout_items'] ?? [];

$subtotal = 0;
foreach ($checkout_items as $item) {
    $price = floatval(str_replace('.', '', $item['price']));
    $quantity = intval($item['quantity']);
    $subtotal += $price * $quantity;
}

$shipping_fee = 10000;
$discount = 0;
$total = $subtotal + $shipping_fee;

// Xử lý mã giảm giá
$discount_message = '';
if (isset($_POST['verify_discount'])) {
    $discount_code = mysqli_real_escape_string($conn, $_POST['discount_code']);
    $sql_discount = "SELECT discount_amount, expiry_date FROM discounts WHERE code = '$discount_code' AND is_active = 1";
    $result_discount = mysqli_query($conn, $sql_discount);
    
    if (mysqli_num_rows($result_discount) > 0) {
        $discount_data = mysqli_fetch_assoc($result_discount);
        $expiry_date = $discount_data['expiry_date'];
        $current_date = date('Y-m-d');
        
        if ($expiry_date >= $current_date) {
            $discount = $discount_data['discount_amount'];
            $total -= $discount;
            $discount_message = "Áp dụng mã giảm giá thành công!";
            $_SESSION['applied_discount'] = $discount;
            $_SESSION['discount_code'] = $discount_code;
        } else {
            $discount_message = "Mã giảm giá đã hết hạn!";
        }
    } else {
        $discount_message = "Mã giảm giá không hợp lệ!";
    }
}

// Load mã giảm giá đã áp dụng nếu có
if (isset($_SESSION['applied_discount'])) {
    $discount = $_SESSION['applied_discount'];
    $total = $subtotal + $shipping_fee - $discount;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['verify_discount'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    $vnp_TxnRef = time() . rand(1000, 9999);
    $sql_order = "INSERT INTO orders (username, total, delivery_address, payment_method, vnpay_txn_ref, status)
              VALUES ('$username', $total, '$delivery_address', '$payment_method', '$vnp_TxnRef', 'pending')";
    if (mysqli_query($conn, $sql_order)) {
        $order_id = mysqli_insert_id($conn);
        foreach ($checkout_items as $item) {
            $name = mysqli_real_escape_string($conn, $item['name']);
            $price = floatval(str_replace('.', '', $item['price']));
            $quantity = intval($item['quantity']);
            $sql_item = "INSERT INTO order_items (order_id, product_name, price, quantity)
                         VALUES ($order_id, '$name', $price, $quantity)";
            mysqli_query($conn, $sql_item);
        }

        $_SESSION['order_id'] = $order_id;
        $_SESSION['total'] = $total;
        unset($_SESSION['applied_discount']);
        unset($_SESSION['discount_code']);

        if ($payment_method == 'vnpay') {
            header("Location: vnpay_create_payment.php");
            exit();
        } else {
            unset($_SESSION['checkout_items']);
            header("Location: thanhtoan.php?success=1");
            exit();
        }
    } else {
        $error = "Lỗi khi đặt hàng: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF424E;
            --primary-hover: #E53945;
            --secondary: #00B4D8;
            --dark: #1E293B;
            --light: #F8FAFC;
            --gray: #64748B;
            --light-gray: #E2E8F0;
            --success: #10B981;
            --warning: #F59E0B;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F5F5FA;
            color: var(--dark);
        }
        
        .checkout-container {
            max-width: 1200px;
        }
        
        .product-item {
            border-bottom: 1px solid var(--light-gray);
        }
        
        .payment-method {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: var(--primary);
            box-shadow: 0 0 0 1px var(--primary);
        }
        
        .payment-method.selected {
            border-color: var(--primary);
            background-color: rgba(255, 66, 78, 0.05);
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 66, 78, 0.3);
        }
        
        .discount-input {
            border: 2px solid var(--light-gray);
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .discount-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 66, 78, 0.2);
        }
        
        .success-message {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success);
        }
        
        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #EF4444;
        }
    </style>
</head>
<body class="bg-gray-100">
<?php include 'navbar.php'; ?>

<div class="container mx-auto px-4 py-8 checkout-container">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column - Delivery Info -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 rounded-full bg-[var(--primary)] text-white flex items-center justify-center mr-3">
                        1
                    </div>
                    <h2 class="text-xl font-bold">Thông Tin Giao Hàng</h2>
                </div>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <p class="font-medium">Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại 3BROPACK.</p>
                        </div>
                        <p class="text-sm mt-2">Mã đơn hàng của bạn là #<?php echo $_SESSION['order_id']; ?>. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="error-message p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <p class="font-medium"><?php echo $error; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($discount_message)): ?>
                    <div class="<?php echo strpos($discount_message, 'thành công') !== false ? 'success-message' : 'error-message'; ?> p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas <?php echo strpos($discount_message, 'thành công') !== false ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500'; ?> mr-2"></i>
                            <p class="font-medium"><?php echo $discount_message; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="checkout-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <input type="text" name="full_name" class="w-full p-3 border rounded-md" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input type="text" name="phone" class="w-full p-3 border rounded-md" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ giao hàng</label>
                        <textarea name="delivery_address" class="w-full p-3 border rounded-md" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 rounded-full bg-[var(--primary)] text-white flex items-center justify-center mr-3">
                            2
                        </div>
                        <h2 class="text-xl font-bold">Phương Thức Thanh Toán</h2>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <label class="payment-method flex items-center p-4 cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" class="mr-3" checked>
                            <div class="flex-1">
                                <div class="font-medium">Thanh toán khi nhận hàng (COD)</div>
                                <div class="text-sm text-gray-500">Bạn chỉ phải thanh toán khi nhận được hàng</div>
                            </div>
                            <i class="fas fa-money-bill-wave text-gray-400"></i>
                        </label>
                        
                        <label class="payment-method flex items-center p-4 cursor-pointer">
                            <input type="radio" name="payment_method" value="vnpay" class="mr-3">
                            <div class="flex-1">
                                <div class="font-medium">VNPay</div>
                                <div class="text-sm text-gray-500">Thanh toán qua cổng VNPay</div>
                            </div>
                            <img src="https://via.placeholder.com/80x30?text=VNPay" alt="VNPay" class="h-6">
                        </label>
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 rounded-full bg-[var(--primary)] text-white flex items-center justify-center mr-3">
                            3
                        </div>
                        <h2 class="text-xl font-bold">Mã Giảm Giá</h2>
                    </div>
                    
                    <div class="flex mb-6">
                        <div class="flex-1 mr-2">
                            <input type="text" name="discount_code" class="w-full p-3 discount-input" 
                                   value="<?php echo isset($_SESSION['discount_code']) ? $_SESSION['discount_code'] : ''; ?>"
                                   placeholder="Nhập mã giảm giá">
                        </div>
                        <button type="submit" name="verify_discount" class="bg-gray-800 text-white px-6 rounded-md whitespace-nowrap">
                            Áp dụng
                        </button>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full py-3 text-lg">
                        ĐẶT HÀNG
                    </button>
                </form>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-lg mb-4">Chính sách mua hàng</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Miễn phí vận chuyển cho đơn hàng từ 500.000₫</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Đổi trả trong vòng 7 ngày nếu sản phẩm lỗi</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Hỗ trợ 24/7 qua hotline 1900 1234</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-6">Đơn Hàng Của Bạn</h2>
                
                <div class="space-y-4 mb-6">
                    <?php foreach ($checkout_items as $item): ?>
                        <?php 
                            $item_price = floatval(str_replace('.', '', $item['price']));
                            $item_total = $item_price * intval($item['quantity']);
                        ?>
                        <div class="product-item flex pb-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden mr-4 flex-shrink-0">
                                <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/80'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-sm text-gray-500">Số lượng: <?php echo intval($item['quantity']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium"><?php echo number_format($item_total, 0, ',', '.'); ?>₫</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="border-t pt-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Tạm tính:</span>
                        <span><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Phí vận chuyển:</span>
                        <span><?php echo number_format($shipping_fee, 0, ',', '.'); ?>₫</span>
                    </div>
                    <?php if ($discount > 0): ?>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Giảm giá:</span>
                            <span class="text-red-500">-<?php echo number_format($discount, 0, ',', '.'); ?>₫</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="border-t pt-4 mb-6">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Tổng cộng:</span>
                        <span class="text-[var(--primary)]"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-md">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                        <div>
                            <p class="text-sm">Bạn có thể kiểm tra tình trạng đơn hàng trong mục <a href="#" class="text-blue-500 underline">Đơn hàng của tôi</a> sau khi đặt hàng thành công.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Highlight selected payment method
    document.querySelectorAll('.payment-method').forEach(method => {
        const radio = method.querySelector('input[type="radio"]');
        
        method.addEventListener('click', () => {
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected');
            });
            method.classList.add('selected');
            radio.checked = true;
        });
        
        if (radio.checked) {
            method.classList.add('selected');
        }
    });
</script>

<?php include 'navbar.php'; ?>
</body>
</html>
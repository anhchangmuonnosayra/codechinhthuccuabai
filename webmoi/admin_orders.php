<?php
include 'db_connect.php';



// Đánh dấu tất cả đơn hàng là đã xem bởi admin
$sql_update_viewed = "UPDATE orders SET viewed_by_admin = 1 WHERE viewed_by_admin = 0";
mysqli_query($conn, $sql_update_viewed);

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    if (mysqli_query($conn, $sql)) {
        $success = "Cập nhật trạng thái đơn hàng thành công!";
    } else {
        $error = "Lỗi khi cập nhật trạng thái: " . mysqli_error($conn);
    }
}

// Lấy danh sách đơn hàng
$sql = "SELECT o.*, u.full_name, u.phone, u.address
        FROM orders o
        JOIN users u ON o.username = u.username
        ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Hệ Thống Tương Lai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: 165 180 252;
            --secondary: 129 140 248;
            --accent: 99 102 241;
            --bg: 249 250 251;
            --text: 17 24 39;
        }
        
        .dark {
            --primary: 99 102 241;
            --secondary: 79 70 229;
            --accent: 67 56 202;
            --bg: 17 24 39;
            --text: 249 250 251;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: rgb(var(--bg));
            color: rgb(var(--text));
            transition: all 0.3s ease;
        }
        
        .futuristic-card {
            background: rgba(var(--bg), 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(var(--primary), 0.2);
            box-shadow: 0 8px 32px 0 rgba(var(--primary), 0.1);
        }
        
        .status-badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wider;
            box-shadow: 0 0 10px rgba(var(--primary), 0.3);
        }
        
        .glow-effect {
            box-shadow: 0 0 15px rgba(var(--primary), 0.5);
        }
        
        .holographic {
            position: relative;
            overflow: hidden;
        }
        
        .holographic::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(var(--primary), 0.1),
                transparent
            );
            transform: rotate(45deg);
            animation: hologram 6s linear infinite;
        }
        
        @keyframes hologram {
            0% { transform: rotate(45deg) translate(-30%, -30%); }
            100% { transform: rotate(45deg) translate(30%, 30%); }
        }
        
        .cyber-button {
            @apply px-4 py-2 rounded-md font-bold transition-all duration-300;
            background: linear-gradient(135deg, rgb(var(--primary)), rgb(var(--secondary)));
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .cyber-button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: rotate(45deg) translate(-30%, -30%); }
            100% { transform: rotate(45deg) translate(30%, 30%); }
        }
        
        .toggle-checkbox:checked {
            @apply right-0 border-indigo-500;
            right: 0;
            background-color: rgb(var(--primary));
        }
        
        .toggle-checkbox:checked + .toggle-label {
            @apply bg-indigo-600;
            background-color: rgb(var(--primary));
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-900/10 to-purple-900/10 dark:from-indigo-900/30 dark:to-purple-900/30">
    <div class="min-h-screen">



        <!-- Main Content -->
        <main class="relative z-0 mx-6 my-6">
            <!-- Notifications -->
            <?php if (isset($success)): ?>
                <div class="futuristic-card mb-6 p-4 border-l-4 border-green-500 rounded-lg animate-fade-in">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-green-500">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200"><?php echo $success; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="futuristic-card mb-6 p-4 border-l-4 border-red-500 rounded-lg animate-fade-in">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-red-500">
                            <i class="fas fa-exclamation-circle text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200"><?php echo $error; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Orders Table -->
            <div class="futuristic-card rounded-xl overflow-hidden">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">MÃ ĐƠN</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">KHÁCH HÀNG</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">TỔNG TIỀN</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">TRẠNG THÁI</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">NGÀY ĐẶT</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">TÁC VỤ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                                <?php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                        'shipped' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                                        'completed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                        'cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
                                    ];
                                    $statusTexts = [
                                        'pending' => 'ĐANG XỬ LÝ',
                                        'shipped' => 'ĐANG VẬN CHUYỂN',
                                        'completed' => 'HOÀN THÀNH',
                                        'cancelled' => 'ĐÃ HỦY'
                                    ];
                                    $statusClass = $statusClasses[$order['status']] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
                                    $statusText = $statusTexts[$order['status']] ?? 'KHÔNG XÁC ĐỊNH';
                                ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                <span class="text-indigo-600 dark:text-indigo-400 font-bold">#<?php echo htmlspecialchars($order['id']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                        <div class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">
                                            <i class="fas fa-phone-alt mr-1"></i>
                                            <?php echo htmlspecialchars($order['phone']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            <?php echo htmlspecialchars($order['delivery_address']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        <?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <i class="fas 
                                                <?php 
                                                    switch($order['status']) {
                                                        case 'pending': echo 'fa-clock'; break;
                                                        case 'shipped': echo 'fa-truck'; break;
                                                        case 'completed': echo 'fa-check-circle'; break;
                                                        case 'cancelled': echo 'fa-times-circle'; break;
                                                        default: echo 'fa-question-circle';
                                                    }
                                                ?> 
                                                mr-1"></i>
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                        <br>
                                        <i class="far fa-clock mr-1 mt-1"></i>
                                        <?php echo date('H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Details Button -->
                                            <button onclick="toggleDetails('details-<?php echo $order['id']; ?>')" 
                                                    class="cyber-button px-3 py-1 text-xs">
                                                <i class="fas fa-expand mr-1"></i> CHI TIẾT
                                            </button>
                                            
                                            <!-- Status Form -->
                                            <form method="POST" class="flex items-center space-x-2">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="text-xs rounded-md bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Đang xử lý</option>
                                                    <option value="shipped" <?php if ($order['status'] == 'shipped') echo 'selected'; ?>>Đang vận chuyển</option>
                                                    <option value="completed" <?php if ($order['status'] == 'completed') echo 'selected'; ?>>Hoàn thành</option>
                                                    <option value="cancelled" <?php if ($order['status'] == 'cancelled') echo 'selected'; ?>>Đã hủy</option>
                                                </select>
                                                <button type="submit" class="cyber-button px-3 py-1 text-xs">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Order Details Row -->
                                <tr id="details-<?php echo $order['id']; ?>" class="hidden bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="6" class="px-6 py-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div class="futuristic-card p-4 rounded-lg">
                                                <h4 class="font-bold text-lg mb-3 text-indigo-600 dark:text-indigo-400 border-b pb-2 border-indigo-200 dark:border-indigo-800">
                                                    <i class="fas fa-info-circle mr-2"></i>THÔNG TIN ĐƠN
                                                </h4>
                                                <div class="grid grid-cols-2 gap-3 text-sm">
                                                    <div class="text-gray-500 dark:text-gray-400">Mã đơn:</div>
                                                    <div class="font-bold">#<?php echo htmlspecialchars($order['id']); ?></div>
                                                    
                                                    <div class="text-gray-500 dark:text-gray-400">PT Thanh toán:</div>
                                                    <div class="font-bold"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                                                    
                                                    <div class="text-gray-500 dark:text-gray-400">Ngày đặt:</div>
                                                    <div class="font-bold"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
                                                    
                                                    <div class="text-gray-500 dark:text-gray-400">Trạng thái:</div>
                                                    <div class="font-bold">
                                                        <span class="status-badge <?php echo $statusClass; ?>">
                                                            <?php echo $statusText; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="futuristic-card p-4 rounded-lg md:col-span-2">
                                                <h4 class="font-bold text-lg mb-3 text-indigo-600 dark:text-indigo-400 border-b pb-2 border-indigo-200 dark:border-indigo-800">
                                                    <i class="fas fa-boxes mr-2"></i>DANH SÁCH SẢN PHẨM
                                                </h4>
                                                <div class="space-y-3">
                                                    <?php
                                                    $order_id = $order['id'];
                                                    $sql_items = "SELECT * FROM order_items WHERE order_id = $order_id";
                                                    $items_result = mysqli_query($conn, $sql_items);
                                                    while ($item = mysqli_fetch_assoc($items_result)):
                                                    ?>
                                                    <div class="flex justify-between items-center p-3 rounded-lg bg-gray-100 dark:bg-gray-700">
                                                        <div>
                                                            <div class="font-bold"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">Số lượng: <?php echo $item['quantity']; ?></div>
                                                        </div>
                                                        <div class="font-bold text-indigo-600 dark:text-indigo-400">
                                                            <?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ
                                                        </div>
                                                    </div>
                                                    <?php endwhile; ?>
                                                    
                                                    <div class="flex justify-between items-center p-3 mt-4 rounded-lg bg-indigo-100 dark:bg-indigo-900">
                                                        <div class="font-bold">TỔNG CỘNG</div>
                                                        <div class="font-bold text-lg text-indigo-700 dark:text-indigo-300">
                                                            <?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <div class="mx-auto h-24 w-24 text-indigo-400 mb-4">
                            <i class="fas fa-box-open text-6xl opacity-50"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-2">KHÔNG CÓ ĐƠN HÀNG</h3>
                        <p class="text-gray-500 dark:text-gray-400">Hệ thống không tìm thấy đơn hàng nào phù hợp</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="futuristic-card mx-6 mt-6 mb-6 rounded-xl p-4 text-center text-xs text-gray-500 dark:text-gray-400">
            HỆ THỐNG QUẢN LÝ ĐƠN HÀNG NEO-V1.0 | © <?php echo date('Y'); ?> BẢN QUYỀN THUỘC VỀ CÔNG TY CỦA BẠN
        </footer>
    </div>

    <script>
        // Toggle dark/light mode
        const toggle = document.getElementById('toggle');
        const html = document.documentElement;
        
        // Check for saved user preference or use system preference
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            toggle.checked = true;
        }
        
        toggle.addEventListener('change', function() {
            if (this.checked) {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        });
        
        // Toggle order details
        function toggleDetails(id) {
            const element = document.getElementById(id);
            element.classList.toggle('hidden');
        }
        
        // Add holographic effect to cards on hover
        const cards = document.querySelectorAll('.futuristic-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('holographic');
            });
            card.addEventListener('mouseleave', () => {
                card.classList.remove('holographic');
            });
        });
    </script>
</body>
</html>
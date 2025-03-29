<?php
include 'db_connect.php';

// Xử lý thêm mã giảm giá
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_discount'])) {
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $discount_amount = floatval($_POST['discount_amount']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "INSERT INTO discounts (code, discount_amount, expiry_date, is_active) 
            VALUES ('$code', $discount_amount, '$expiry_date', $is_active)";
    if (mysqli_query($conn, $sql)) {
        $success = "Thêm mã giảm giá thành công!";
    } else {
        $error = "Lỗi khi thêm mã giảm giá: " . mysqli_error($conn);
    }
}

// Xử lý xóa mã giảm giá
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql_delete = "DELETE FROM discounts WHERE id = $delete_id";
    if (mysqli_query($conn, $sql_delete)) {
        $success = "Xóa mã giảm giá thành công!";
    } else {
        $error = "Lỗi khi xóa mã giảm giá: " . mysqli_error($conn);
    }
}

// Lấy danh sách mã giảm giá
$sql_discounts = "SELECT * FROM discounts ORDER BY expiry_date DESC";
$result_discounts = mysqli_query($conn, $sql_discounts);
?>

<!DOCTYPE html>
<html lang="vi" class="<?php echo isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true' ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Mã Giảm Giá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .smooth-transition {
            transition: all 0.3s ease-in-out;
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .dark .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-dark-900 text-gray-800 dark:text-gray-200 smooth-transition min-h-screen">
    <div class="container mx-auto px-4 py-8">


        <!-- Add Discount Card -->
        <div class="bg-white dark:bg-dark-800 rounded-xl card-shadow p-6 mb-8 smooth-transition">
            <h2 class="text-xl font-semibold mb-4 text-primary-700 dark:text-primary-400 flex items-center">
                <i class="fas fa-tag mr-2"></i> Thêm Mã Giảm Giá Mới
            </h2>
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Mã Giảm Giá</label>
                        <input type="text" name="code" class="w-full p-3 border border-gray-300 dark:border-dark-700 rounded-lg bg-white dark:bg-dark-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent smooth-transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Số Tiền Giảm (VNĐ)</label>
                        <input type="number" name="discount_amount" step="1000" class="w-full p-3 border border-gray-300 dark:border-dark-700 rounded-lg bg-white dark:bg-dark-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent smooth-transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Ngày Hết Hạn</label>
                        <input type="date" name="expiry_date" class="w-full p-3 border border-gray-300 dark:border-dark-700 rounded-lg bg-white dark:bg-dark-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent smooth-transition" required>
                    </div>
                    <div class="flex items-center justify-center md:justify-start">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" class="sr-only" checked>
                            <div class="toggle-bg bg-gray-200 dark:bg-dark-700 border border-gray-300 dark:border-dark-600 h-6 w-11 rounded-full smooth-transition"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Kích Hoạt</span>
                        </div>
                    </div>
                </div>
                <button type="submit" name="add_discount" class="mt-4 bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium smooth-transition flex items-center justify-center">
                    <i class="fas fa-plus-circle mr-2"></i> Thêm Mã
                </button>
            </form>
        </div>

        <!-- Discount List Card -->
        <div class="bg-white dark:bg-dark-800 rounded-xl card-shadow p-6 smooth-transition">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-primary-700 dark:text-primary-400 flex items-center">
                    <i class="fas fa-list-ul mr-2"></i> Danh Sách Mã Giảm Giá
                </h2>
                <div class="relative">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <?php echo mysqli_num_rows($result_discounts); ?> mã giảm giá
                    </span>
                </div>
            </div>

            <?php if (mysqli_num_rows($result_discounts) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-100 dark:bg-dark-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Mã Giảm Giá</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Số Tiền Giảm</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ngày Hết Hạn</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Trạng Thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-700">
                            <?php while ($discount = mysqli_fetch_assoc($result_discounts)): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-700/50 smooth-transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-primary-600 dark:text-primary-400">
                                        <?php echo htmlspecialchars($discount['code']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo number_format($discount['discount_amount'], 0, ',', '.'); ?> VNĐ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo date('d/m/Y', strtotime($discount['expiry_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full smooth-transition
                                            <?php echo $discount['is_active'] ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'; ?>">
                                            <?php echo $discount['is_active'] ? 'Kích Hoạt' : 'Không Kích Hoạt'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="admin_dashboard.php?page=giamgia&delete_id=<?php echo $discount['id']; ?>" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');"
                                           class="text-red-500 hover:text-red-700 dark:hover:text-red-400 smooth-transition flex items-center">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-tag text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400">Chưa có mã giảm giá nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        // Check for saved theme preference or use preferred color scheme
        function checkTheme() {
            if (localStorage.getItem('darkMode') === 'true' || 
                (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
        }
        
        // Initial check
        checkTheme();
        
        // Toggle theme
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('darkMode', html.classList.contains('dark'));
        });
        
        // Toggle switch styling
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const toggleBg = this.nextElementSibling;
                if (this.checked) {
                    toggleBg.classList.add('bg-primary-600', 'dark:bg-secondary-600');
                    toggleBg.classList.remove('bg-gray-200', 'dark:bg-dark-700');
                } else {
                    toggleBg.classList.remove('bg-primary-600', 'dark:bg-secondary-600');
                    toggleBg.classList.add('bg-gray-200', 'dark:bg-dark-700');
                }
            });
            
            // Initialize toggle state
            const toggleBg = checkbox.nextElementSibling;
            if (checkbox.checked) {
                toggleBg.classList.add('bg-primary-600', 'dark:bg-secondary-600');
            }
        });
    </script>
</body>
</html>
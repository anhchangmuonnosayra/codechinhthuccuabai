<?php
include 'db_connect.php';

// Xử lý thêm thông báo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_notification'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $auto_close = intval($_POST['auto_close']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "INSERT INTO notifications (title, content, auto_close, is_active) 
            VALUES ('$title', '$content', $auto_close, $is_active)";
    if (mysqli_query($conn, $sql)) {
        $success = "Thêm thông báo thành công!";
    } else {
        $error = "Lỗi khi thêm thông báo: " . mysqli_error($conn);
    }
}

// Xử lý xóa thông báo
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql_delete = "DELETE FROM notifications WHERE id = $delete_id";
    if (mysqli_query($conn, $sql_delete)) {
        $success = "Xóa thông báo thành công!";
    } else {
        $error = "Lỗi khi xóa thông báo: " . mysqli_error($conn);
    }
}

// Lấy danh sách thông báo
$sql_notifications = "SELECT * FROM notifications ORDER BY id DESC";
$result_notifications = mysqli_query($conn, $sql_notifications);
?>

<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thông Báo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    transitionProperty: {
                        'width': 'width',
                        'spacing': 'margin, padding',
                    },
                }
            }
        }
    </script>
    <style>
        .smooth-transition {
            transition: all 0.3s ease-in-out;
        }
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .light .gradient-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-full bg-gray-50 dark:bg-dark-900 smooth-transition">
    <div class="relative">

        

                <!-- Messages -->
                <?php if (isset($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 smooth-transition" role="alert">
                        <span class="block sm:inline"><?php echo $success; ?></span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 smooth-transition" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times cursor-pointer" onclick="this.parentElement.parentElement.style.display='none'"></i>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Add Notification Form -->
                <div class="bg-white dark:bg-dark-800 rounded-xl shadow-md p-6 mb-8 smooth-transition">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Thêm Thông Báo Mới</h2>
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiêu Đề</label>
                                <input type="text" name="title" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-dark-700 dark:text-white smooth-transition" required placeholder="Ví dụ: Ưu đãi đặc biệt">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nội Dung</label>
                                <input type="text" name="content" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-dark-700 dark:text-white smooth-transition" required placeholder="Ví dụ: Giảm 50% toàn bộ sản phẩm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tự Động Tắt (giây)</label>
                                <input type="number" name="auto_close" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-dark-700 dark:text-white smooth-transition" min="0" value="0" placeholder="0 = Không tự tắt">
                            </div>
                        </div>
                        <div class="mt-6 flex items-center">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-dark-700 dark:border-gray-600 smooth-transition" checked>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Kích Hoạt</span>
                            </label>
                        </div>
                        <div class="mt-6">
                            <button type="submit" name="add_notification" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-md smooth-transition transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50">
                                <i class="fas fa-plus-circle mr-2"></i> Thêm Thông Báo
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notifications List -->
                <div class="bg-white dark:bg-dark-800 rounded-xl shadow-md overflow-hidden smooth-transition">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Danh Sách Thông Báo</h2>
                    </div>
                    
                    <?php if (mysqli_num_rows($result_notifications) > 0): ?>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php while ($notification = mysqli_fetch_assoc($result_notifications)): ?>
                                <div class="p-6 hover:bg-gray-50 dark:hover:bg-dark-700 smooth-transition notification-card">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div class="mb-4 md:mb-0">
                                            <h3 class="text-lg font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($notification['title']); ?></h3>
                                            <p class="text-gray-600 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($notification['content']); ?></p>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                                            <div class="flex items-center mb-2 sm:mb-0">
                                                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Tự tắt:</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    <?php echo $notification['auto_close'] > 0 ? $notification['auto_close'] . ' giây' : 'Không'; ?>
                                                </span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                    <?php echo $notification['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; ?>">
                                                    <?php echo $notification['is_active'] ? 'Đang hoạt động' : 'Đã tắt'; ?>
                                                </span>
                                            </div>
                                            <div class="mt-2 sm:mt-0 sm:ml-4">
                                                <a href="admin_dashboard.php?page=thongbao&delete_id=<?php echo $notification['id']; ?>" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');"
                                                   class="text-red-500 hover:text-red-700 dark:hover:text-red-400 smooth-transition">
                                                   <i class="fas fa-trash-alt mr-1"></i> Xóa
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-6 text-center">
                            <div class="text-gray-500 dark:text-gray-400">
                                <i class="fas fa-bell-slash text-4xl mb-3"></i>
                                <p class="text-lg">Chưa có thông báo nào</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme management
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeSelect = document.getElementById('themeSelect');
        const html = document.documentElement;

        // Check for saved theme preference or use system preference
        const savedTheme = localStorage.getItem('theme') || 'auto';
        themeSelect.value = savedTheme;

        function applyTheme(theme) {
            if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            } else {
                html.classList.remove('dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }
        }

        // Initialize theme
        applyTheme(savedTheme);

        // Toggle button click handler
        themeToggle.addEventListener('click', () => {
            const isDark = html.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            themeSelect.value = newTheme;
            applyTheme(newTheme);
        });

        // Select change handler
        themeSelect.addEventListener('change', (e) => {
            const selectedTheme = e.target.value;
            localStorage.setItem('theme', selectedTheme);
            applyTheme(selectedTheme);
        });

        // Watch for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (localStorage.getItem('theme') === 'auto') {
                applyTheme('auto');
            }
        });
    </script>
</body>
</html>
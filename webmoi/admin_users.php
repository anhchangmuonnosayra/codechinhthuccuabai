<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --primary-blue-dark: #2563eb;
            --primary-purple: #8b5cf6;
            --primary-purple-dark: #7c3aed;
            --bg-light: #f8fafc;
            --bg-dark: #0f172a;
            --card-light: #ffffff;
            --card-dark: #1e293b;
            --text-light: #334155;
            --text-dark: #e2e8f0;
            --sidebar-light: #ffffff;
            --sidebar-dark: #1e293b;
            --accent-light: #e2e8f0;
            --accent-dark: #334155;
        }

        .dark {
            --primary: var(--primary-purple-dark);
            --primary-hover: var(--primary-purple);
            --bg: var(--bg-dark);
            --card: var(--card-dark);
            --text: var(--text-dark);
            --sidebar: var(--sidebar-dark);
            --accent: var(--accent-dark);
        }

        .light {
            --primary: var(--primary-blue);
            --primary-hover: var(--primary-blue-dark);
            --bg: var(--bg-light);
            --card: var(--card-light);
            --text: var(--text-light);
            --sidebar: var(--sidebar-light);
            --accent: var(--accent-light);
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            transition: background-color 0.3s ease, color 0.3s ease;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .sidebar {
            background-color: var(--sidebar);
            transition: background-color 0.3s ease;
        }

        .card {
            background-color: var(--card);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 1px solid var(--accent);
        }

        .table-container {
            overflow: hidden;
            border-radius: 12px;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            font-size: 0.75rem;
        }

        .custom-table td {
            padding: 14px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--accent);
        }

        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }

        .custom-table tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .dark .custom-table tbody tr:hover {
            background-color: rgba(139, 92, 246, 0.1);
        }

        .btn {
            transition: all 0.2s ease;
            transform: scale(1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            color: white;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            color: white;
        }

        .alert {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modern toggle switch */
        .theme-switch-wrapper {
            display: flex;
            align-items: center;
        }

        .theme-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin: 0 10px;
        }

        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(145deg, #4f46e5, #8b5cf6);
            transition: .4s;
            border-radius: 34px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background: linear-gradient(145deg, #8b5cf6, #4f46e5);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Modern input styles */
        .input-field {
            background-color: var(--card);
            border: 1px solid var(--accent);
            color: var(--text);
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .dark .input-field:focus {
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3);
        }

        /* Badge styles */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Loading spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating action button */
        .fab {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            z-index: 50;
        }

        .fab:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            background-color: var(--primary-hover);
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--accent);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Pulse animation for new items */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <div class="container mx-auto px-4 py-8">
        

            <?php
            include 'db_connect.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
                $user_id = intval($_POST['user_id']);
                $sql = "DELETE FROM users WHERE id = $user_id";
                if (mysqli_query($conn, $sql)) {
                    $success = "Xóa người dùng thành công!";
                } else {
                    $error = "Lỗi khi xóa người dùng: " . mysqli_error($conn);
                }
            }

            $sql = "SELECT * FROM users ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);
            ?>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tổng số người dùng</p>
                            <p class="text-2xl font-bold mt-1"><?php echo mysqli_num_rows($result); ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <i class="fas fa-users text-blue-500 dark:text-blue-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Người dùng mới (7 ngày)</p>
                            <p class="text-2xl font-bold mt-1">0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                            <i class="fas fa-user-plus text-purple-500 dark:text-purple-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hoạt động gần đây</p>
                            <p class="text-2xl font-bold mt-1">0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <i class="fas fa-clock text-green-500 dark:text-green-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <div class="card p-6 mb-6 fade-in">
                <?php if (isset($success)): ?>
                    <div class="alert bg-green-100 dark:bg-green-900 border-l-4 border-green-500 dark:border-green-400 text-green-700 dark:text-green-200 p-4 mb-6 rounded-lg flex items-start" role="alert">
                        <i class="fas fa-check-circle mr-3 mt-1 text-green-500 dark:text-green-300"></i>
                        <div>
                            <p class="font-bold">Thành công!</p>
                            <p><?php echo $success; ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert bg-red-100 dark:bg-red-900 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-200 p-4 mb-6 rounded-lg flex items-start" role="alert">
                        <i class="fas fa-exclamation-circle mr-3 mt-1 text-red-500 dark:text-red-300"></i>
                        <div>
                            <p class="font-bold">Lỗi!</p>
                            <p><?php echo $error; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- User Table -->
            <div class="card overflow-hidden fade-in">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Danh sách người dùng</h2>
                </div>
                
                <div class="table-container">
                    <div class="overflow-x-auto">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th class="text-left">ID</th>
                                    <th class="text-left">Tên Người Dùng</th>
                                    <th class="text-left">Email</th>
                                    <th class="text-left">Họ và Tên</th>
                                    <th class="text-left">Trạng thái</th>
                                    <th class="text-center">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                                    <tr class="hover:bg-opacity-10">
                                        <td class="font-medium"><?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?></td>
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-indigo-500 dark:text-indigo-300 text-sm"></i>
                                                </div>
                                                <span><?php echo htmlspecialchars($user['username']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <i class="fas fa-circle text-xs mr-1"></i> Hoạt động
                                            </span>
                                        </td>
                                        <td class="text-center space-x-2">
                                            <button class="btn btn-primary px-3 py-1 rounded-lg text-sm">
                                                <i class="fas fa-edit mr-1"></i> Sửa
                                            </button>
                                            <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id'] ?? 0; ?>">
                                                <button type="submit" class="btn btn-danger px-3 py-1 rounded-lg text-sm">
                                                    <i class="fas fa-trash-alt mr-1"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                

    <!-- Floating Action Button -->
    <button class="fab">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;
            
            // Check for saved theme preference or use system preference
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme) {
                html.classList.add(savedTheme);
                themeToggle.checked = savedTheme === 'dark';
            } else {
                // If no saved theme, use system preference
                const initialTheme = systemPrefersDark ? 'dark' : 'light';
                html.classList.add(initialTheme);
                themeToggle.checked = systemPrefersDark;
                localStorage.setItem('theme', initialTheme);
            }
            
            // Handle theme toggle change
            themeToggle.addEventListener('change', function() {
                if (this.checked) {
                    html.classList.replace('light', 'dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    html.classList.replace('dark', 'light');
                    localStorage.setItem('theme', 'light');
                }
            });
            
            // Watch for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('theme')) {
                    const newTheme = e.matches ? 'dark' : 'light';
                    html.classList.replace(html.classList.contains('dark') ? 'dark' : 'light', newTheme);
                    themeToggle.checked = e.matches;
                }
            });

            // Add smooth transitions when page loads
            setTimeout(() => {
                document.body.classList.remove('opacity-0');
            }, 100);
        });
    </script>
</body>
</html>
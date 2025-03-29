<?php
session_start();
include 'db_connect.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Lấy thông tin trang hiện tại
$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Admin - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#b9e6fe',
                            300: '#7cd4fd',
                            400: '#36bffa',
                            500: '#0ca5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter var', 'sans-serif'],
                    },
                },
            },
            plugins: [],
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .dark-mode .glass-effect {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(55, 65, 81, 0.5);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans transition-colors duration-200" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: window.innerWidth >= 1024 }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      :class="{'dark bg-gray-900 text-white': darkMode}">

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js" defer></script>

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar Mobile Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden"></div>

    <!-- Sidebar -->
    <aside x-bind:class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
          class="fixed inset-y-0 left-0 z-30 w-64 transform transition-transform duration-300 lg:translate-x-0 lg:relative lg:flex glass-effect dark:bg-gray-800 overflow-y-auto scrollbar-hide">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <svg class="h-8 w-8 text-primary-600 dark:text-primary-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-xl font-bold">3BROPACK</span>
                </div>
                <button @click="sidebarOpen = false" class="p-2 rounded-md lg:hidden hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-4 px-2">
                <div class="px-3 mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quản lý hệ thống</span>
                </div>
                <ul class="space-y-1">
                    <li>
                        <a href="admin_dashboard.php?page=dashboard" 
                          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'dashboard' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
                            <i class="fas fa-chart-line w-5 h-5 mr-2"></i>
                            <span>Doanh Thu</span>
                            <?php if($page == 'dashboard'): ?>
                            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php?page=orders" 
                          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'orders' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
                            <i class="fas fa-shopping-cart w-5 h-5 mr-2"></i>
                            <span>Quản Lý Đơn Hàng</span>
                            <?php if($page == 'orders'): ?>
                            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php?page=users" 
                          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'users' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
                            <i class="fas fa-users w-5 h-5 mr-2"></i>
                            <span>Quản Lý Người Dùng</span>
                            <?php if($page == 'users'): ?>
                            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php?page=products" 
                          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'products' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
                            <i class="fas fa-box w-5 h-5 mr-2"></i>
                            <span>Quản Lý Sản Phẩm</span>
                            <?php if($page == 'products'): ?>
                            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
                            <?php endif; ?>
                        </a>
                    </li>

    <!-- ... Các menu hiện có ... -->
    <li>
        <a href="admin_dashboard.php?page=questions" 
          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'questions' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
            <i class="fas fa-question-circle w-5 h-5 mr-2"></i>
            <span>Quản Lý Câu Hỏi</span>
            <?php if($page == 'questions'): ?>
            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
            <?php endif; ?>
        </a>
    </li>

                </ul>

                <div class="px-3 mt-8 mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tiếp thị</span>
                </div>
                <ul class="space-y-1">
                    <li>
                        <a href="admin_dashboard.php?page=giamgia" 
                          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'giamgia' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
                            <i class="fas fa-tags w-5 h-5 mr-2"></i>
                            <span>Mã Giảm Giá</span>
                            <?php if($page == 'giamgia'): ?>
                            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="admin_dashboard.php?page=thongbao" 
                          class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo $page == 'thongbao' ? 'bg-primary-500 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200'; ?>">
                            <i class="fas fa-bell w-5 h-5 mr-2"></i>
                            <span>Quản Lý Thông Báo</span>
                            <?php if($page == 'thongbao'): ?>
                            <span class="ml-auto flex h-2 w-2 rounded-full bg-white"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile and Logout -->
            <div class="border-t dark:border-gray-700 p-4">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                        <?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-sm font-medium dark:text-white"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                        <a href="admin_logout.php" class="text-xs text-red-500 hover:text-red-600 hover:underline">Đăng xuất</a>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <header class="h-16 bg-white dark:bg-gray-800 shadow-sm glass-effect z-10">
            <div class="flex items-center justify-between h-full px-4">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="text-gray-600 dark:text-gray-300 p-2 rounded-md lg:hidden hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Page Title -->
                <h1 class="text-xl font-semibold text-gray-800 dark:text-white lg:hidden">
                    <?php
                    switch ($page) {
                        case 'dashboard': echo 'Doanh Thu'; break;
                        case 'orders': echo 'Quản Lý Đơn Hàng'; break;
                        case 'users': echo 'Quản Lý Người Dùng'; break;
                        case 'giamgia': echo 'Quản Lý Mã Giảm Giá'; break;
                        case 'thongbao': echo 'Quản Lý Thông Báo'; break;
                        case 'products': echo 'Quản Lý Sản Phẩm'; break;
                        case 'question': echo 'Quản Lý Câu Hỏi'; break;
                        default: echo 'Doanh Thu';
                    }
                    ?>
                </h1>

                <!-- Search Field -->
                <div class="hidden md:flex relative flex-1 max-w-xl mx-4">
                    <input type="text" placeholder="Tìm kiếm..." class="w-full bg-gray-100 dark:bg-gray-700 border-0 rounded-lg pl-10 py-2 pr-4 focus:ring-2 focus:ring-primary-500 dark:text-white">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

                <!-- Right side controls -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>
                    
                    <!-- Dark mode toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full">
                        <svg x-show="!darkMode" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white hidden lg:block">
                    <?php
                    switch ($page) {
                        case 'dashboard': echo 'Doanh Thu'; break;
                        case 'orders': echo 'Quản Lý Đơn Hàng'; break;
                        case 'users': echo 'Quản Lý Người Dùng'; break;
                        case 'giamgia': echo 'Quản Lý Mã Giảm Giá'; break;
                        case 'thongbao': echo 'Quản Lý Thông Báo'; break;
                        case 'products': echo 'Quản Lý Sản Phẩm'; break;
                        case 'question': echo 'Quản Lý Câu Hỏi'; break;
                         default: echo 'Doanh Thu';
                    }
                    ?>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <?php echo date('l, d F Y'); ?>
                </p>
            </div>

            <!-- Content switching based on page -->
            <?php
            switch ($page) {
                case 'dashboard':
                    // Tính tổng doanh thu
                    $sql_total_revenue = "SELECT SUM(total) as total_revenue FROM orders WHERE status = 'completed'";
                    $result_total_revenue = mysqli_query($conn, $sql_total_revenue);
                    if (!$result_total_revenue) {
                        echo "<p class='text-red-500'>Lỗi truy vấn tổng doanh thu: " . mysqli_error($conn) . "</p>";
                        break;
                    }
                    $total_revenue = mysqli_fetch_assoc($result_total_revenue)['total_revenue'] ?? 0;

                    // Doanh thu 7 ngày gần nhất
                    $sql_revenue = "SELECT DATE(created_at) as order_date, SUM(total) as daily_revenue 
                                    FROM orders 
                                    WHERE status = 'completed' 
                                    GROUP BY DATE(created_at) 
                                    ORDER BY order_date DESC 
                                    LIMIT 7";
                    $result_revenue = mysqli_query($conn, $sql_revenue);
                    if (!$result_revenue) {
                        echo "<p class='text-red-500'>Lỗi truy vấn doanh thu: " . mysqli_error($conn) . "</p>";
                        break;
                    }
                    $revenue_data = [];
                    while ($row = mysqli_fetch_assoc($result_revenue)) {
                        $revenue_data[$row['order_date']] = $row['daily_revenue'];
                    }
                    
                    // Đếm tổng đơn hàng
                    $sql_total_orders = "SELECT COUNT(*) as total_orders FROM orders";
                    $result_total_orders = mysqli_query($conn, $sql_total_orders);
                    $total_orders = mysqli_fetch_assoc($result_total_orders)['total_orders'] ?? 0;
                    
                    // Đếm tổng người dùng
                    $sql_total_users = "SELECT COUNT(*) as total_users FROM users";
                    $result_total_users = mysqli_query($conn, $sql_total_users);
                    $total_users = mysqli_fetch_assoc($result_total_users)['total_users'] ?? 0;
                    ?>
                    
                    <!-- Analytics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <!-- Tổng doanh thu -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 glass-effect">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng doanh thu</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?php echo number_format($total_revenue, 0, ',', '.'); ?> ₫</p>
                                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-2">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                        </svg>
                                        +12.5% so với tháng trước
                                    </p>
                                </div>
                                <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-full">
                                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tổng đơn hàng -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 glass-effect">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng đơn hàng</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?php echo number_format($total_orders, 0, ',', '.'); ?></p>
                                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-2">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                        </svg>
                                        +8.1% so với tháng trước
                                    </p>
                                </div>
                                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tổng khách hàng -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 glass-effect">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng người dùng</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?php echo number_format($total_users, 0, ',', '.'); ?></p>
                                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-2">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                        </svg>
                                        +5.2% so với tháng trước
                                    </p>
                                </div>
                                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Trung bình đơn hàng -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 glass-effect">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Trung bình đơn hàng</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        <?php echo $total_orders > 0 ? number_format($total_revenue / $total_orders, 0, ',', '.') : 0; ?> ₫
                                    </p>
                                    <p class="text-xs text-red-600 dark:text-red-400 flex items-center mt-2">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"></path>
                                        </svg>
                                        -2.3% so với tháng trước
                                    </p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Revenue Chart (Wider) -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 lg:col-span-2 glass-effect">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Doanh thu 7 ngày gần nhất</h3>
                                <div class="flex space-x-2">
                                    <button class="px-3 py-1 text-xs font-medium rounded-full bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400">7 ngày</button>
                                    <button class="px-3 py-1 text-xs font-medium rounded-full text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">30 ngày</button>
                                    <button class="px-3 py-1 text-xs font-medium rounded-full text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">1 năm</button>
                                </div>
                            </div>
                            <div id="revenue-chart" class="h-80"></div>
                        </div>
                        
                        <!-- Sales Distribution (Smaller) -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 glass-effect">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Phân bố đơn hàng</h3>
                                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="distribution-chart" class="h-80"></div>
                        </div>
                    </div>
                    
                    <script>
                        // Revenue Chart
                        document.addEventListener('DOMContentLoaded', function() {
                            var options = {
                                series: [{
                                    name: 'Doanh thu',
                                    data: <?php echo json_encode(array_values($revenue_data)); ?>
                                }],
                                chart: {
                                    type: 'area',
                                    height: 320,
                                    fontFamily: 'Inter, sans-serif',
                                    toolbar: {
                                        show: false
                                    },
                                    zoom: {
                                        enabled: false
                                    }
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                colors: ['#0ca5e9'],
                                fill: {
                                    type: 'gradient',
                                    gradient: {
                                        shade: 'dark',
                                        type: 'vertical',
                                        shadeIntensity: 0.3,
                                        opacityFrom: 0.5,
                                        opacityTo: 0.1,
                                        stops: [0, 100]
                                    }
                                },
                                grid: {
                                    borderColor: '#e0e0e0',
                                    strokeDashArray: 4,
                                    xaxis: {
                                        lines: {
                                            show: true
                                        }
                                    },
                                    yaxis: {
                                        lines: {
                                            show: true
                                        }
                                    },
                                },
                                xaxis: {
                                    categories: <?php echo json_encode(array_keys($revenue_data)); ?>,
                                    labels: {
                                        style: {
                                            colors: '#64748b',
                                            fontSize: '12px',
                                            fontFamily: 'Inter, sans-serif',
                                        },
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        formatter: function (value) {
                                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(value);
                                        },
                                        style: {
                                            colors: '#64748b',
                                            fontSize: '12px',
                                            fontFamily: 'Inter, sans-serif',
                                        },
                                    },
                                },
                                tooltip: {
                                    y: {
                                        formatter: function (value) {
                                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(value);
                                        }
                                    }
                                }
                            };

                            var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
                            chart.render();
                            
                            // Distribution Chart
                            var distributionOptions = {
                                series: [44, 55, 13, 33],
                                chart: {
                                    height: 320,
                                    type: 'donut',
                                    fontFamily: 'Inter, sans-serif',
                                },
                                labels: ['TP. HCM', 'Hà Nội', 'Đà Nẵng', 'Khác'],
                                colors: ['#0ca5e9', '#22c55e', '#f59e0b', '#64748b'],
                                dataLabels: {
                                    enabled: false
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '65%',
                                            labels: {
                                                show: true,
                                                name: {
                                                    show: true,
                                                    fontSize: '22px',
                                                    fontFamily: 'Inter, sans-serif',
                                                    fontWeight: 600,
                                                    color: undefined,
                                                    offsetY: -10,
                                                },
                                                value: {
                                                    show: true,
                                                    fontSize: '16px',
                                                    fontFamily: 'Inter, sans-serif',
                                                    fontWeight: 400,
                                                    color: undefined,
                                                    offsetY: 16,
                                                    formatter: function (val) {
                                                    return val + "%"
                                                    }
                                                },
                                                total: {
                                                    show: true,
                                                    showAlways: false,
                                                    label: 'Tổng',
                                                    fontSize: '14px',
                                                    fontFamily: 'Inter, sans-serif',
                                                    fontWeight: 400,
                                                    color: '#64748b',
                                                    formatter: function (w) {
                                                        return w.globals.seriesTotals.reduce((a, b) => {
                                                            return a + b
                                                        }, 0) + ' đơn'
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                legend: {
                                    position: 'bottom',
                                    fontFamily: 'Inter, sans-serif',
                                }
                            };

                            var distributionChart = new ApexCharts(document.querySelector("#distribution-chart"), distributionOptions);
                            distributionChart.render();
                            
                            // Update charts when theme changes
                            document.addEventListener('alpine:init', () => {
                                Alpine.effect(() => {
                                    const isDark = Alpine.store('darkMode');
                                    chart.updateOptions({
                                        grid: {
                                            borderColor: isDark ? '#374151' : '#e0e0e0',
                                        },
                                        xaxis: {
                                            labels: {
                                                style: {
                                                    colors: isDark ? '#94a3b8' : '#64748b',
                                                }
                                            }
                                        },
                                        yaxis: {
                                            labels: {
                                                style: {
                                                    colors: isDark ? '#94a3b8' : '#64748b',
                                                }
                                            }
                                        }
                                    });
                                });
                            });
                        });
                    </script>
                    <?php
                     
                    break;
                case 'orders':
                    include 'admin_orders.php';
                    break;
                case 'users':
                    include 'admin_users.php';
                    break;
                case 'giamgia':
                    include 'admin_giamgia.php';
                    break;
                case 'thongbao':
                    include 'admin_thongbao.php';
                    break;
                    case 'questions':
                        include 'admin_questions.php';
                        break;    
                case 'products':
                    include 'admin_add_product.php'; // Tích hợp file quản lý sản phẩm
                    break;
                default:
                    include 'admin_orders.php';
                
            }
            ?>
        </main>
    </div>
</div>

<?php
if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
    mysqli_close($conn);
}
?>
</body>
</html>
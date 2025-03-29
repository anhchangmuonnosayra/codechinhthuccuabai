<?php
include 'db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Xử lý theme preference
if (isset($_POST['toggle_theme'])) {
    $_SESSION['dark_mode'] = !isset($_SESSION['dark_mode']) ? true : !$_SESSION['dark_mode'];
}

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = htmlspecialchars($_POST['name']);
    $price = floatval($_POST['price']);
    $category = htmlspecialchars($_POST['category']);
    $size = intval($_POST['size']);
    
    // Xử lý upload ảnh
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $error = "File không phải là ảnh.";
    } elseif ($_FILES["image"]["size"] > 5000000) {
        $error = "File ảnh quá lớn.";
    } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $error = "Chỉ chấp nhận file JPG, JPEG, PNG hoặc GIF.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO product (name, price, category, size, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsss", $name, $price, $category, $size, $target_file);
            if ($stmt->execute()) {
                $message = "Thêm sản phẩm thành công!";
            } else {
                $error = "Lỗi khi thêm sản phẩm: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Lỗi khi tải ảnh lên.";
        }
    }
}

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM product ORDER BY created_at DESC";
$result_products = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi" class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .smooth-transition {
            transition: all 0.3s ease-in-out;
        }
        .dark .dark\:bg-dark-primary {
            background-color: #111827;
        }
        .dark .dark\:bg-dark-secondary {
            background-color: #1f2937;
        }
        .dark .dark\:text-light {
            color: #f3f4f6;
        }
        .dark .dark\:border-dark {
            border-color: #374151;
        }
        .dark .dark\:hover\:bg-dark-hover:hover {
            background-color: #374151;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-dark-primary smooth-transition min-h-screen">
    <div class="container mx-auto px-4 py-8">


        <!-- Thông báo -->
        <?php if (isset($message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg smooth-transition">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p><?php echo $message; ?></p>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg smooth-transition">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p><?php echo $error; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form thêm sản phẩm -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl shadow-lg smooth-transition border border-gray-200 dark:border-dark">
                    <div class="flex items-center mb-6">
                        <div class="bg-primary-100 dark:bg-primary-900 p-3 rounded-full mr-4">
                            <i class="fas fa-plus text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-light">Thêm Sản Phẩm Mới</h2>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">Tên Sản Phẩm</label>
                            <input class="w-full px-4 py-2 border border-gray-300 dark:border-dark rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-light smooth-transition" id="name" name="name" required type="text"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="price">Giá (VNĐ)</label>
                            <input class="w-full px-4 py-2 border border-gray-300 dark:border-dark rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-light smooth-transition" id="price" name="price" required type="number" step="0.01"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="category">Danh Mục</label>
                            <select class="w-full px-4 py-2 border border-gray-300 dark:border-dark rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-light smooth-transition" id="category" name="category" required>
                                <option value="walking">Giày Thể Thao Đi Bộ</option>
                                <option value="badminton">Giày Cầu Lông</option>
                                <option value="volleyball">Giày Bóng Chuyền</option>
                                <option value="artificial-turf">Giày Sân Nhân Tạo</option>
                                <option value="natural-grass">Giày Cỏ Tự Nhiên</option>
                                <option value="futsal">Giày Fusal</option>
                                <option value="adult">Giày Người Lớn</option>
                                <option value="children">Giày Trẻ Em</option>
                                <option value="jacket">Áo Khoác</option>
                                <option value="dress">Đầm Nữ</option>
                                <option value="t-shirt">Áo Thun</option>
                                <option value="bag">Túi Xách</option>
                                <option value="shirt">Áo Sơ Mi</option>
                                <option value="shorts">Quần Short</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="size">Size</label>
                            <input class="w-full px-4 py-2 border border-gray-300 dark:border-dark rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-light smooth-transition" id="size" name="size" required type="number"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="image">Hình Ảnh</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="image" class="flex flex-col w-full h-32 border-2 border-gray-300 dark:border-dark border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 smooth-transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Chọn hoặc kéo thả ảnh vào đây</p>
                                    </div>
                                    <input id="image" name="image" type="file" class="hidden" accept="image/*" required/>
                                </label>
                            </div>
                        </div>
                        <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg smooth-transition flex items-center justify-center" type="submit">
                            <i class="fas fa-plus mr-2"></i> Thêm Sản Phẩm
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-dark-secondary p-6 rounded-xl shadow-lg smooth-transition border border-gray-200 dark:border-dark">
                    <div class="flex items-center mb-6">
                        <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full mr-4">
                            <i class="fas fa-list text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-light">Danh Sách Sản Phẩm</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-left text-gray-700 dark:text-gray-300">
                                    <th class="px-4 py-3 rounded-tl-lg">ID</th>
                                    <th class="px-4 py-3">Tên Sản Phẩm</th>
                                    <th class="px-4 py-3">Giá</th>
                                    <th class="px-4 py-3">Danh Mục</th>
                                    <th class="px-4 py-3">Size</th>
                                    <th class="px-4 py-3">Hình Ảnh</th>
                                    <th class="px-4 py-3 rounded-tr-lg">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php while ($product = mysqli_fetch_assoc($result_products)): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 smooth-transition">
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?php echo $product['id']; ?></td>
                                        <td class="px-4 py-3 text-gray-800 dark:text-light font-medium"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td class="px-4 py-3 text-primary-600 dark:text-primary-400 font-medium"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300"><?php echo $product['size']; ?></td>
                                        <td class="px-4 py-3">
                                            <img alt="Hình ảnh của <?php echo htmlspecialchars($product['name']); ?>" class="w-12 h-12 object-cover rounded-lg" src="<?php echo htmlspecialchars($product['image_url']); ?>"/>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex space-x-2">
                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 smooth-transition p-2 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 smooth-transition p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add animation to notifications
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.bg-green-100, .bg-red-100');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.classList.add('opacity-0');
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
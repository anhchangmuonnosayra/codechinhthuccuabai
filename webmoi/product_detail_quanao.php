<?php
session_start();
include 'db_connect.php';

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id == 0) {
    header("Location: quanao.php");
    exit();
}

// Lấy thông tin sản phẩm từ database
$sql = "SELECT * FROM product WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();  
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: quanao.php");
    exit();
}

// Lấy danh sách ảnh phụ
$additional_images = !empty($product['additional_images']) ? json_decode($product['additional_images'], true) : [];
$all_images = array_merge([$product['image_url']], $additional_images);

// Định nghĩa danh mục
$categories = [
    'jacket' => 'Áo Khoác',
    'dress' => 'Đầm Nữ',
    't-shirt' => 'Áo Thun',
    'bag' => 'Túi Xách',
    'shirt' => 'Áo Sơ Mi',
    'shorts' => 'Quần Short',
    'accessory' => 'Phụ Kiện'
];
$category_name = $categories[$product['category']] ?? 'Không xác định';

// Kiểm tra người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$has_purchased = false;

// Kiểm tra đã mua hàng nếu đã đăng nhập
if ($user_id) {
    $check_purchase = "SELECT 1 FROM orders o 
                      JOIN order_items oi ON o.id = oi.order_id 
                      WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'completed'";
    $stmt = $conn->prepare($check_purchase);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $has_purchased = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}

// Xử lý form đánh giá
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review']) && $has_purchased) {
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    $sql = "INSERT INTO product_reviews (product_id, user_id, rating, comment) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();
    
    header("Refresh:0"); // Tải lại trang
}

// Xử lý form câu hỏi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_question'])) {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Khách';
    
    $sql = "INSERT INTO product_questions (product_id, user_id, user_name, question) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $product_id, $user_id, $user_name, $question);
    $stmt->execute();
    $stmt->close();
    
    header("Refresh:0"); // Tải lại trang
}

// Lấy đánh giá sản phẩm
$reviews = [];
$avg_rating = 0;
$sql = "SELECT r.*, u.username FROM product_reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.product_id = ? ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Tính điểm trung bình
if (count($reviews) > 0) {
    $total = array_reduce($reviews, function($carry, $item) {
        return $carry + $item['rating'];
    }, 0);
    $avg_rating = round($total / count($reviews), 1);
}

// Lấy câu hỏi sản phẩm
$questions = [];
$sql = "SELECT * FROM product_questions WHERE product_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .thumbnail { 
            cursor: pointer; 
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .thumbnail:hover, .thumbnail.active { 
            border-color: #3b82f6; 
            transform: scale(1.05);
        }
        .star-rating .star {
            color: #d1d5db;
        }
        .star-rating .star.active {
            color: #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <img alt="Logo 3BROPACK" class="h-8 w-8 mr-2" src="3propack-removebg-preview.png"/>
                <span class="font-bold text-lg">3BROPACK</span>
            </div>
            <div class="hidden md:flex space-x-6 text-sm">
                <div class="flex items-center"><i class="fas fa-money-bill-wave mr-2"></i> Thanh toán khi nhận</div>
                <div class="flex items-center"><i class="fas fa-truck mr-2"></i> Giao hàng toàn quốc</div>
                <div class="flex items-center"><i class="fas fa-undo mr-2"></i> Đổi trả 7 ngày</div>
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="flex items-center">
                        <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="logout.php" class="ml-4 hover:underline">Đăng xuất</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Breadcrumb -->
            <div class="px-6 py-4 bg-gray-50">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="index.php" class="text-blue-600 hover:text-blue-800">Trang chủ</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="quan_ao.php" class="text-blue-600 hover:text-blue-800">Quần áo</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-gray-500"><?php echo htmlspecialchars($product['name']); ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Product Detail -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Product Images -->
                    <div>
                        <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden">
                            <img id="main-image" src="<?php echo htmlspecialchars($all_images[0]); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-full h-96 object-contain">
                        </div>
                        
                        <?php if (count($all_images) > 1): ?>
                        <div class="flex space-x-2 overflow-x-auto py-2">
                            <?php foreach ($all_images as $index => $image): ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     alt="Ảnh phụ <?php echo $index + 1; ?>" 
                                     class="thumbnail w-16 h-16 object-cover rounded cursor-pointer <?php echo $index === 0 ? 'active border-blue-500' : ''; ?>"
                                     onclick="changeMainImage('<?php echo htmlspecialchars($image); ?>', this)">
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product Info -->
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h1>
                        
                        <div class="flex items-center mb-4">
                            <div class="flex items-center mr-4">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $avg_rating ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                <?php endfor; ?>
                                <span class="text-gray-600 ml-2">(<?php echo count($reviews); ?>)</span>
                            </div>
                            <span class="text-gray-500">Mã SP: <?php echo $product['id']; ?></span>
                        </div>
                        
                        <div class="mb-6">
                            <span class="text-3xl font-bold text-red-600">
                                <?php echo number_format($product['price'], 0, ',', '.'); ?>₫
                            </span>
                            
                        </div>
                        
                       
                        <div class="mb-6">
                            <div class="flex items-center mb-2">
                                <span class="font-semibold mr-4">Kích thước:</span>
                                <div class="flex flex-wrap gap-2">
                                    <?php 
                                    $sizes = ['S', 'M', 'L', 'XL'];
                                    foreach ($sizes as $size): 
                                    ?>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="size" value="<?php echo $size; ?>" 
                                                   class="hidden peer" <?php echo $size === 'M' ? 'checked' : ''; ?>>
                                            <span class="px-4 py-2 border rounded-lg cursor-pointer peer-checked:bg-blue-600 peer-checked:text-white">
                                                <?php echo $size; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="flex items-center border rounded-lg overflow-hidden">
                                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300" onclick="updateQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="10" 
                                       class="w-16 text-center border-0 focus:ring-0">
                                <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300" onclick="updateQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                            <button id="add-to-cart" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition">
                                <i class="fas fa-shopping-cart mr-2"></i> Thêm vào giỏ hàng
                            </button>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex items-center text-gray-600 mb-2">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <span>Bảo hành 6 tháng</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-box-open mr-2"></i>
                                <span>Miễn phí đổi trả trong 7 ngày</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews and Q&A Section -->
                <div class="mt-12 border-t pt-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Reviews -->
                        <div>
                            <h2 class="text-xl font-bold mb-6">Đánh giá sản phẩm</h2>
                            
                            <?php if ($has_purchased): ?>
                            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                <h3 class="font-semibold mb-3">Viết đánh giá của bạn</h3>
                                <form method="POST">
                                    <div class="mb-4">
                                        <div class="flex items-center mb-2">
                                            <span class="mr-3">Đánh giá:</span>
                                            <div class="star-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <label>
                                                        <input type="radio" name="rating" value="<?php echo $i; ?>" required class="hidden">
                                                        <i class="fas fa-star cursor-pointer text-2xl mx-1 star" data-value="<?php echo $i; ?>"></i>
                                                    </label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <textarea name="comment" rows="3" class="w-full border rounded p-2" placeholder="Nhận xét của bạn..." required></textarea>
                                    </div>
                                    <button type="submit" name="submit_review" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        Gửi đánh giá
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (count($reviews) > 0): ?>
                                <?php foreach ($reviews as $review): ?>
                                <div class="border-b pb-4 mb-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-semibold"><?php echo htmlspecialchars($review['username']); ?></span>
                                            <div class="flex mt-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <span class="text-gray-500 text-sm">
                                            <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-700"><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500">Chưa có đánh giá nào cho sản phẩm này.</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Questions -->
                        <div>
                            <h2 class="text-xl font-bold mb-6">Hỏi đáp về sản phẩm</h2>
                            
                            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                <h3 class="font-semibold mb-3">Đặt câu hỏi</h3>
                                <form method="POST">
                                    <textarea name="question" rows="3" class="w-full border rounded p-2" placeholder="Câu hỏi của bạn..." required></textarea>
                                    <button type="submit" name="submit_question" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        Gửi câu hỏi
                                    </button>
                                </form>
                            </div>
                            
                            <?php if (count($questions) > 0): ?>
                                <?php foreach ($questions as $question): ?>
                                <div class="border-b pb-4 mb-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-semibold"><?php echo htmlspecialchars($question['user_name']); ?></span>
                                        <span class="text-gray-500 text-sm">
                                            <?php echo date('d/m/Y', strtotime($question['created_at'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($question['question']); ?></p>
                                    
                                    <?php if (!empty($question['answer'])): ?>
                                    <div class="bg-blue-50 p-3 rounded mt-2">
                                        <p class="font-semibold text-blue-800">Phản hồi từ cửa hàng:</p>
                                        <p class="text-blue-700"><?php echo htmlspecialchars($question['answer']); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500">Chưa có câu hỏi nào cho sản phẩm này.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Về 3BROPACK</h3>
                    <p class="mb-4">Chuyên cung cấp quần áo thể thao chất lượng cao với giá cả hợp lý.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-blue-400"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-blue-400"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-blue-400"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Thông tin</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-blue-400">Giới thiệu</a></li>
                        <li><a href="#" class="hover:text-blue-400">Sản phẩm</a></li>
                        <li><a href="#" class="hover:text-blue-400">Khuyến mãi</a></li>
                        <li><a href="#" class="hover:text-blue-400">Tin tức</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Chính sách</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-blue-400">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-blue-400">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="hover:text-blue-400">Chính sách đổi trả</a></li>
                        <li><a href="#" class="hover:text-blue-400">Hướng dẫn mua hàng</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Liên hệ</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i> 123 Đường Thể Thao, TP.HCM
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2"></i> 0123 456 789
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i> info@3bropack.com
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>© 2023 3BROPACK. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Thay đổi ảnh chính khi click ảnh phụ
        function changeMainImage(src, element) {
            document.getElementById('main-image').src = src;
            
            // Xóa active class từ tất cả ảnh phụ
            document.querySelectorAll('.thumbnail').forEach(img => {
                img.classList.remove('active', 'border-blue-500');
            });
            
            // Thêm active class cho ảnh được click
            element.classList.add('active', 'border-blue-500');
        }
        
        // Cập nhật số lượng sản phẩm
        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            let newValue = parseInt(input.value) + change;
            
            if (newValue < 1) newValue = 1;
            if (newValue > 10) newValue = 10;
            
            input.value = newValue;
        }
        
        // Xử lý thêm vào giỏ hàng
        document.getElementById('add-to-cart').addEventListener('click', function() {
            const productId = <?php echo $product['id']; ?>;
            const productName = "<?php echo addslashes($product['name']); ?>";
            const price = <?php echo $product['price']; ?>;
            const image = "<?php echo addslashes($all_images[0]); ?>";
            const quantity = parseInt(document.getElementById('quantity').value);
            const size = document.querySelector('input[name="size"]:checked').value;
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Kiểm tra xem sản phẩm đã có trong giỏ chưa
            const existingItem = cart.find(item => 
                item.id === productId && item.size === size
            );
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: price,
                    image: image,
                    size: size,
                    quantity: quantity
                });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Hiển thị thông báo
            alert(`Đã thêm ${quantity} ${productName} (Size ${size}) vào giỏ hàng!`);
            
            // Cập nhật số lượng trên icon giỏ hàng
            updateCartCount();
        });
        
        // Cập nhật số lượng sản phẩm trong giỏ
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            
            // Code để cập nhật số lượng trên icon giỏ hàng
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = totalItems;
            }
        }
        
        // Xử lý rating stars
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('mouseover', function() {
                const value = this.dataset.value;
                highlightStars(value);
            });
            
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                document.querySelector('input[name="rating"]:checked')?.removeAttribute('checked');
                document.querySelector(`input[name="rating"][value="${value}"]`).checked = true;
            });
        });
        
        function highlightStars(value) {
            document.querySelectorAll('.star').forEach(star => {
                if (star.dataset.value <= value) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
        
        // Khởi tạo giỏ hàng khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
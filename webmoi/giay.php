<?php
session_start();
include 'db_connect.php';

// Lấy tất cả sản phẩm giày từ database để xác định giá tối thiểu và tối đa
$sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM product WHERE category IN ('walking', 'badminton', 'volleyball', 'artificial-turf', 'natural-grass', 'futsal', 'adult', 'children')";
$result_range = mysqli_query($conn, $sql);
$price_range = mysqli_fetch_assoc($result_range);
$min_price = $price_range['min_price'] ?? 0;
$max_price = $price_range['max_price'] ?? 1000000; // Giá trị mặc định nếu không có sản phẩm

// Lấy tất cả sản phẩm giày
$sql = "SELECT * FROM product WHERE category IN ('walking', 'badminton', 'volleyball', 'artificial-turf', 'natural-grass', 'futsal', 'adult', 'children')";
$result_products = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giày - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background:linear-gradient(90deg, #ff4d4d,rgb(223, 120, 192));;
        }
        /* Gradient background for top bar */
        .top-bar {  
            background: linear-gradient(90deg, #ff4d4d,rgb(223, 120, 192));;
            color: white;
        }
        /* Hero section styling */
        .hero-section {
            position: relative;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.3)), url('https://www.asics.com/dw/image/v2/BBTN_PRD/on/demandware.static/-/Sites-asics-eu-Library/default/dw20dd5ce3/1_asics/hero-banner/how-long-does-it-take.jpg');
            background-size: cover;
            background-position: center;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }
        .hero-section .cta-button {
            background: #ff4d4d;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            transition: background 0.3s ease;
        }
        .hero-section .cta-button:hover {
            background: #ff1a1a;
        }
        /* Filter buttons */
        .filter-btn {
            background: #e0e0e0;
            color: #333;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .filter-btn.active, .filter-btn:hover {
            background: #ff8c1a;
            color: white;
        }
        /* Product card styling */
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        .product-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        .product-card p {
            font-size: 1.1rem;
            color: #ff4d4d;
            font-weight: 600;
        }
        .product-card .add-to-cart-btn {
            background: #ff8c1a;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        .product-card .add-to-cart-btn:hover {
            background: #ff6b00;
        }
        /* Footer */
        footer {
            background: linear-gradient(90deg, #1a1a1a, #333);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
<!-- Top Bar -->
<div class="top-bar py-3">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center">
            <img alt="Logo của shop 3BROPACK" class="h-10 w-10 mr-3" src="3propack-removebg-preview.png"/>
            <span class="font-bold text-xl">3BROPACK</span>
        </div>
        <div class="flex space-x-6 text-sm">
            <div class="flex items-center"><i class="fas fa-money-bill-wave mr-2"></i><span>Thanh toán khi nhận hàng</span></div>
            <div class="flex items-center"><i class="fas fa-truck mr-2"></i><span>Giao hàng toàn quốc</span></div>
            <div class="flex items-center"><i class="fas fa-undo mr-2"></i><span>Trả hàng trong 3 ngày</span></div>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="flex items-center">
                    <span class="font-semibold">Chào mừng, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a class="ml-4 hover:underline" href="logout.php">Đăng xuất</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div>
        <h1>Tim Trang Thiết Bị Thể Thao Của Bạn</h1>
        <p>Khám phá sản phẩm thể thao đa dạng để nâng tầm phong cách và hiệu suất.</p>
        <a href="#" class="cta-button">Mua Ngay</a>
    </div>
</section>

<!-- Product Section -->
<section class="content-wrapper">
    <div class="container max-w-6xl mx-auto px-4 py-16">
        <h2 class="text-4xl font-bold text-center mb-10 text-gray-800">Sản Phẩm Nổi Bật</h2>

        <!-- Bộ lọc danh mục giày -->
        <div class="mb-8 flex justify-center gap-3 flex-wrap">
            <button class="filter-btn active" data-category="all">Tất Cả</button>
            <button class="filter-btn" data-category="walking">Giày Đi Bộ</button>
            <button class="filter-btn" data-category="badminton">Giày Cầu Lông</button>
            <button class="filter-btn" data-category="volleyball">Giày Bóng Chuyền</button>
            <button class="filter-btn" data-category="artificial-turf">Giày Sân Nhân Tạo</button>
            <button class="filter-btn" data-category="natural-grass">Giày Cỏ Tự Nhiên</button>
            <button class="filter-btn" data-category="futsal">Giày Futsal</button>
            <button class="filter-btn" data-category="adult">Giày Người Lớn</button>
            <button class="filter-btn" data-category="children">Giày Trẻ Em</button>
        </div>

        <!-- Bộ lọc giá tiền với thanh kéo -->
        <div class="mb-8 max-w-md mx-auto">
            <label class="block text-gray-700 mb-3 font-semibold">Khoảng Giá (VNĐ):</label>
            <div id="price-slider" class="mb-4"></div>
            <div class="flex justify-between text-gray-600 font-medium">
                <span id="min-price-value"><?php echo number_format($min_price, 0, ',', '.'); ?></span>
                <span id="max-price-value"><?php echo number_format($max_price, 0, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Thanh tìm kiếm -->
        <div class="mb-8">
            <div class="relative max-w-md mx-auto">
                <input type="text" id="searchInput" class="w-full p-3 pl-12 border rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-sm" placeholder="Tìm kiếm sản phẩm...">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div id="product-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($product = mysqli_fetch_assoc($result_products)): ?>
                <div class="product-card" 
                     data-category="<?php echo htmlspecialchars($product['category']); ?>" 
                     data-price="<?php echo $product['price']; ?>">
                    <img alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full" src="<?php echo htmlspecialchars($product['image_url']); ?>"/>
                    <div class="p-5">
                        <h3 class="mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="mb-4"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                        <div class="flex justify-between items-center">
                            <button class="add-to-cart-btn">Thêm vào Giỏ</button>
                            <div class="flex space-x-3">
                                <a class="text-gray-600 hover:text-red-500" href="#" title="Thêm vào Yêu Thích"><i class="fas fa-heart"></i></a>
                                <a class="text-gray-600 hover:text-blue-500" href="product_detail_giay.php?id=<?php echo $product['id']; ?>" title="Xem Chi Tiết"><i class="fas fa-info-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container mx-auto px-4">
        <p>© 2025 3BROPACK. Bản quyền thuộc về chúng tôi.</p>
    </div>
</footer>

<!-- Thêm script cho noUiSlider -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const priceSlider = document.getElementById('price-slider');
        const minPriceValue = document.getElementById('min-price-value');
        const maxPriceValue = document.getElementById('max-price-value');
        const productContainer = document.getElementById('product-container');
        const products = Array.from(productContainer.children);
        const filterButtons = document.querySelectorAll('.filter-btn');

        // Khởi tạo thanh kéo giá
        noUiSlider.create(priceSlider, {
            start: [<?php echo $min_price; ?>, <?php echo $max_price; ?>],
            connect: true,
            range: {
                'min': <?php echo $min_price; ?>,
                'max': <?php echo $max_price; ?>
            },
            step: 10000,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        // Cập nhật giá trị hiển thị khi kéo thanh
        priceSlider.noUiSlider.on('update', function (values, handle) {
            if (handle === 0) {
                minPriceValue.textContent = values[0].toLocaleString('vi-VN');
            } else {
                maxPriceValue.textContent = values[1].toLocaleString('vi-VN');
            }
            filterProducts();
        });

        // Hàm lọc sản phẩm
        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            const priceRange = priceSlider.noUiSlider.get();
            const minPrice = parseFloat(priceRange[0]);
            const maxPrice = parseFloat(priceRange[1]);
            const activeCategory = document.querySelector('.filter-btn.active')?.dataset.category || 'all';

            products.forEach(product => {
                const title = product.querySelector('h3').textContent.toLowerCase();
                const price = parseFloat(product.dataset.price);
                const category = product.dataset.category;

                const matchesSearch = title.includes(searchTerm);
                const matchesCategory = activeCategory === 'all' || category === activeCategory;
                const matchesPrice = price >= minPrice && price <= maxPrice;

                product.style.display = (matchesSearch && matchesCategory && matchesPrice) ? '' : 'none';
            });
        }

        // Xử lý tìm kiếm
        searchInput.addEventListener('input', filterProducts);

        // Xử lý nút lọc danh mục
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                filterButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                filterProducts();
            });
        });

        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function () {
                const productDiv = this.closest('.product-card');
                const name = productDiv.querySelector('h3').textContent;
                const price = parseFloat(productDiv.querySelector('p').textContent.replace(/[^\d]/g, ''));
                const img = productDiv.querySelector('img').src;
                const desc = `Sản phẩm ${name.toLowerCase()} chất lượng cao.`;

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                const existingItem = cart.find(item => item.name === name);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({ name, price, img, desc, quantity: 1 });
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                alert(`${name} đã được thêm vào giỏ hàng!`);
                window.location.href = 'giohang.php';
            });
        });
    });
</script>

<?php mysqli_close($conn); ?>
</body>
</html>
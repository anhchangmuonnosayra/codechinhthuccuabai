<?php
session_start();
include 'db_connect.php';

// Lấy khoảng giá từ database
$sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM product WHERE category IN ('jacket', 'dress', 't-shirt', 'bag', 'shirt', 'shorts', 'accessory')";
$result_range = mysqli_query($conn, $sql);
$price_range = mysqli_fetch_assoc($result_range);
$min_price = $price_range['min_price'] ?? 0;
$max_price = $price_range['max_price'] ?? 1000000;
$sql = "SELECT * FROM product WHERE category IN ('jacket', 'dress', 't-shirt', 'bag', 'shirt', 'shorts', 'accessory')";
$result_products = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quần Áo - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(90deg, #ff2e63,rgb(50, 225, 213));
            color: #fff;
        }
        .top-bar {
            background: linear-gradient(90deg, #ff2e63,rgb(50, 225, 213));
            box-shadow: 0 4px 15px rgba(255, 46, 99, 0.4);
        }
       
        .hero-overlay {
            background: linear-gradient(to bottom, rgba(51, 15, 25, 0.78), rgba(135, 207, 220, 0.3));
        }
        .filter-btn {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(153, 8, 8, 0.2);
        }
        .filter-btn.active {
            background: #ff2e63 !important;
            border-color: #ff006e;
        }
        .product-card {
            background: #2d3748;
            transition: all 0.3s ease;
            border: 1px solid #4a5568;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 46, 99, 0.2);
            border-color:rgb(69, 185, 195);
        }
        .btn-add {
            background: linear-gradient(45deg,rgb(211, 93, 25), #ff006e);
        }
        .btn-add:hover {
            background: linear-gradient(45deg,rgb(216, 114, 59),rgba(34, 214, 181, 0.84));
            transform: scale(1.05);
        }
        footer {
            background:rgb(25, 20, 13);
            border-top: 2px solid #ff2e63;
        }
    </style>
</head>
<body>
<!-- Top Bar -->
<div class="top-bar py-3">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center">
            <img alt="Logo" class="h-10 w-10 mr-3" src="3propack-removebg-preview.png"/>
            <span class="font-extrabold text-xl tracking-wider">3BROPACK</span>
        </div>
        
        <div class="flex space-x-6 text-sm">
            <div class="flex items-center"><i class="fas fa-money-bill-wave mr-2"></i>Thanh toán COD</div>
            <div class="flex items-center"><i class="fas fa-truck mr-2"></i>Giao hàng toàn quốc</div>
            <div class="flex items-center"><i class="fas fa-undo mr-2"></i>Đổi trả 3 ngày</div>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="flex items-center">
                    <span class="font-semibold">Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a class="ml-4 hover:text-pink-200 transition" href="logout.php">Đăng xuất</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'navbar.php'; ?>

<!-- Hero Section -->
<section class="relative">
    <img alt="Thời trang" class="w-full h-[500px] object-cover" src="https://theme.hstatic.net/200000247969/1000814323/14/collection_banner.jpg?v=384"/>
    <div class="absolute inset-0 hero-overlay flex flex-col justify-center items-center text-center">
        <h1 class="text-5xl font-extrabold mb-4 tracking-tight drop-shadow">BỘ SƯU TẬP MỚI</h1>
        <p class="text-xl mb-8 drop-shadow">Định nghĩa phong cách của bạn</p>
        <a class="btn-add text-white px-8 py-3 rounded-full text-lg font-semibold transition" href="#">MUA NGAY</a>
    </div>
</section>

<!-- Product Section -->
<section class="container mx-auto px-4 py-16">
    <h2 class="text-4xl font-extrabold text-center mb-8 tracking-wide">SẢN PHẨM HOT</h2>

    <!-- Bộ lọc danh mục -->
    <div class="mb-8 flex justify-center gap-4 flex-wrap">
        <button class="filter-btn active text-white px-5 py-2 rounded-full font-semibold" data-category="all">Tất Cả</button>
        <button class="filter-btn bg-gray-700 text-white px-5 py-2 rounded-full font-semibold" data-category="jacket">Áo Khoác</button>
        <button class="filter-btn bg-gray-700 text-white px-5 py-2 rounded-full font-semibold" data-category="dress">Đầm Nữ</button>
        <button class="filter-btn bg-gray-700 text-white px-5 py-2 rounded-full font-semibold" data-category="t-shirt">Áo Thun</button>
        <button class="filter-btn bg-gray-700 text-white px-5 py-2 rounded-full font-semibold" data-category="bag">Túi Xách</button>
        <button class="filter-btn bg-gray-700 text-white px-5 py-2 rounded-full font-semibold" data-category="shirt">Áo Sơ Mi</button>
        <button class="filter-btn bg-gray-700 text-white px-5 py-2 rounded-full font-semibold" data-category="shorts">Quần Short</button>
    </div>

    <!-- Bộ lọc giá -->
    <div class="mb-8 max-w-md mx-auto">
        <label class="block text-pink-200 font-semibold mb-3">KHOẢNG GIÁ (VNĐ):</label>
        <div id="price-slider" class="mb-4"></div>
        <div class="flex justify-between text-pink-100 font-medium">
            <span id="min-price-value"><?php echo number_format($min_price, 0, ',', '.'); ?></span>
            <span id="max-price-value"><?php echo number_format($max_price, 0, ',', '.'); ?></span>
        </div>
    </div>

    <!-- Thanh tìm kiếm -->
    <div class="mb-8 max-w-md mx-auto relative">
        <input type="text" id="searchInput" class="w-full p-3 pl-12 rounded-full bg-gray-800 border border-gray-600 focus:ring-2 focus:ring-pink-500 focus:outline-none text-white placeholder-gray-400" placeholder="Tìm kiếm sản phẩm...">
        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-pink-400"></i>
    </div>

    <!-- Danh sách sản phẩm -->
    <div id="product-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php while ($product = mysqli_fetch_assoc($result_products)): ?>
            <div class="product-card rounded-xl overflow-hidden" 
                 data-category="<?php echo htmlspecialchars($product['category']); ?>" 
                 data-price="<?php echo $product['price']; ?>">
                <img alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-72 object-cover" src="<?php echo htmlspecialchars($product['image_url']); ?>"/>
                <div class="p-5">
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-pink-300 font-medium mb-4"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                    <div class="flex justify-between items-center">
                        <button class="btn-add text-white px-4 py-2 rounded-full text-sm font-semibold transition">Thêm vào Giỏ</button>
                        <div class="flex gap-3">
                            <a class="text-gray-400 hover:text-pink-500 transition" href="#" title="Yêu Thích"><i class="fas fa-heart"></i></a>
                            <a class="text-gray-400 hover:text-pink-500 transition" href="product_detail_quanao.php?id=<?php echo $product['id']; ?>" title="Chi Tiết"><i class="fas fa-info-circle"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Footer -->
<footer class="py-8">
    <div class="container mx-auto px-4">
        <p class="text-center text-gray-300">© 2025 3BROPACK. All rights reserved.</p>
    </div>
</footer>

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
                to: value => Math.round(value),
                from: value => Number(value)
            }
        });

        priceSlider.noUiSlider.on('update', function (values, handle) {
            if (handle === 0) minPriceValue.textContent = values[0].toLocaleString('vi-VN');
            else maxPriceValue.textContent = values[1].toLocaleString('vi-VN');
            filterProducts();
        });

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

        searchInput.addEventListener('input', filterProducts);

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                filterProducts();
            });
        });

        document.querySelectorAll('.btn-add').forEach(button => {
            button.addEventListener('click', function () {
                const productDiv = this.closest('.product-card');
                const name = productDiv.querySelector('h3').textContent;
                const price = parseFloat(productDiv.querySelector('p').textContent.replace(/[^\d]/g, ''));
                const img = productDiv.querySelector('img').src;
                const desc = `Sản phẩm ${name.toLowerCase()} chất lượng cao.`;

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                const existingItem = cart.find(item => item.name === name);
                if (existingItem) existingItem.quantity += 1;
                else cart.push({ name, price, img, desc, quantity: 1 });
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
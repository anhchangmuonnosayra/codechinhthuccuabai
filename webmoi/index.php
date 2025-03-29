<?php
session_start();
include 'db_connect.php';
include 'navbar.php';

// Lấy thông báo
$sql_notifications = "SELECT id, title, content, auto_close FROM notifications WHERE is_active = 1";
$result_notifications = mysqli_query($conn, $sql_notifications);

// Lấy 8 sản phẩm ngẫu nhiên (tăng từ 5 lên 8)

    $sql_random_products = "SELECT * FROM product ORDER BY RAND() LIMIT 10";
    $result_random_products = mysqli_query($conn, $sql_random_products);
    $random_products = [];
    while ($row = mysqli_fetch_assoc($result_random_products)) {
        $random_products[] = $row;
    }


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="3BROPACK - Cửa hàng thể thao hàng đầu với giày dép, quần áo và phụ kiện thể thao chất lượng cao"/>
    <title>3BROPACK - Thể Thao & Phong Cách</title>
    
    <!-- Preload resources -->
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    
    <!-- CSS & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    
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
            line-height: 1.6;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .search-bar {
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .search-bar:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 66, 78, 0.2);
        }
        
        /* Hero Slider */
        .hero-slider {
            height: 450px;
        }
        
        .hero-slide {
            background-size: cover;
            background-position: center;
            transition: opacity 0.8s ease-in-out;
        }
        
        /* Product Card */
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--primary);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .product-discount {
            color: var(--primary);
            font-weight: 600;
        }
        
        .product-price {
            color: var(--primary);
            font-weight: 700;
        }
        
        .product-original-price {
            text-decoration: line-through;
            color: var(--gray);
            font-size: 14px;
        }
        
        .product-rating {
            color: var(--warning);
            font-size: 14px;
        }
        
        .product-sold {
            color: var(--gray);
            font-size: 14px;
        }
        
        /* Category Card */
        .category-card {
            background: white;
            border-radius: 10px;
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Button */
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 66, 78, 0.3);
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-secondary:hover {
            background: #0095B6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 180, 216, 0.3);
        }
        
        /* Flash Sale */
        .flash-sale {
            background: linear-gradient(135deg, #FF424E 0%, #FF6B7D 100%);
            color: white;
        }
        
        .countdown {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            padding: 2px 8px;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-slider {
                height: 300px;
            }
            
            .product-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Header -->
    <header class="header py-3">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Logo & Search -->
                <div class="flex items-center justify-between w-full md:w-auto">
                    <div class="flex items-center">
                        <img alt="3BROPACK Logo" class="h-10 w-auto mr-3" src="3propack-removebg-preview.png" loading="lazy"/>
                        <span class="font-extrabold text-2xl text-[var(--primary)]">3BROPACK</span>
                    </div>
                    <button class="md:hidden text-2xl text-gray-600" id="mobile-menu-button">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                
                
                <!-- User Actions -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="taikhoan.php" class="flex flex-col items-center text-gray-700 hover:text-[var(--primary)] text-xs">
                        <i class="fas fa-user text-xl mb-1"></i>
                        <span>Tài khoản</span>
                    </a>
                    <a href="giohang.php" class="flex flex-col items-center text-gray-700 hover:text-[var(--primary)] text-xs relative">
                        <i class="fas fa-shopping-cart text-xl mb-1"></i>
                        <span>Giỏ hàng</span>
                        <span class="absolute -top-2 -right-2 bg-[var(--primary)] text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    
                            

    <!-- Notifications -->
    <?php while ($notification = mysqli_fetch_assoc($result_notifications)): ?>
        <div class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-xl z-50 max-w-xs border-l-4 border-[var(--primary)] transform transition-all duration-300 animate-fadeIn" id="notification_<?php echo $notification['id']; ?>">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-[var(--dark)]"><?php echo htmlspecialchars($notification['title']); ?></h3>
                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($notification['content']); ?></p>
                </div>
                <button class="ml-2 text-gray-400 hover:text-gray-600" onclick="document.getElementById('notification_<?php echo $notification['id']; ?>').style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php if ($notification['auto_close'] > 0): ?>
                <div class="h-1 bg-gray-200 mt-2 w-full">
                    <div class="h-full bg-[var(--primary)] notification-timer" style="width: 100%"></div>
                </div>
                <script>
                    setTimeout(() => {
                        document.getElementById('notification_<?php echo $notification['id']; ?>').style.opacity = '0';
                        setTimeout(() => {
                            document.getElementById('notification_<?php echo $notification['id']; ?>').style.display = 'none';
                        }, 300);
                    }, <?php echo $notification['auto_close'] * 1000; ?>);
                    
                    // Animation for timer bar
                    const timerBar = document.querySelector('#notification_<?php echo $notification['id']; ?> .notification-timer');
                    let width = 100;
                    const interval = setInterval(() => {
                        width -= 0.5;
                        timerBar.style.width = width + '%';
                        if (width <= 0) clearInterval(interval);
                    }, <?php echo $notification['auto_close'] * 10; ?>);
                </script>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

    <!-- Main Content -->
    <main>
        <!-- Hero Slider -->
        <section class="hero-slider relative overflow-hidden">
            <div class="absolute inset-0 hero-slide active" style="background-image: url('https://www.asics.com/dw/image/v2/BBTN_PRD/on/demandware.static/-/Sites-asics-us-Library/default/dw6687def5/asics_homepage_desktop_hero_c24_050122.jpg')">
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/20 flex items-center">
                    <div class="container mx-auto px-4 text-white">
                        <h1 class="text-3xl md:text-5xl font-bold mb-4 max-w-2xl">Tìm Trang Thiết Bị Thể Thao Của Bạn</h1>
                        <p class="text-lg md:text-xl mb-6 max-w-xl">Khám phá sản phẩm thể thao đa dạng để nâng tầm phong cách và hiệu suất.</p>
                        <a href="quanao.php" class="btn-primary inline-block">Mua Ngay</a>
                    </div>
                </div>
            </div>
            <div class="absolute inset-0 hero-slide" style="background-image: url('https://www.asics.com/on/demandware.static/-/Sites-asics-eu-Library/default/dw2a0124c1/firstspirit/media/ss22/03__march/gel_nimbus_24/asics-n24-male-hp-hero-03092022-1.jpg')">
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/20 flex items-center">
                    <div class="container mx-auto px-4 text-white">
                        <h1 class="text-3xl md:text-5xl font-bold mb-4 max-w-2xl">Chuẩn Bị Cho Trận Đấu</h1>
                        <p class="text-lg md:text-xl mb-6 max-w-xl">Thiết bị chất lượng cao để đưa trận đấu lên tầm mới.</p>
                        <a href="giay.php" class="btn-primary inline-block">Mua Ngay</a>
                    </div>
                </div>
            </div>
            <div class="absolute inset-0 hero-slide" style="background-image: url('https://www.runpack.fr/wp-content/uploads/2020/09/saucony-ride-13-mutant.png')">
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/20 flex items-center">
                    <div class="container mx-auto px-4 text-white">
                        <h1 class="text-3xl md:text-5xl font-bold mb-4 max-w-2xl">Giữ Vững Sức Khỏe</h1>
                        <p class="text-lg md:text-xl mb-6 max-w-xl">Trang bị tốt nhất để duy trì vóc dáng.</p>
                        <a href="giay.php" class="btn-primary inline-block">Mua Ngay</a>
                    </div>
                </div>
            </div>
            
            <!-- Slider Controls -->
            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-10">
                <button class="w-3 h-3 rounded-full bg-white opacity-50 slider-dot active" data-index="0"></button>
                <button class="w-3 h-3 rounded-full bg-white opacity-50 slider-dot" data-index="1"></button>
                <button class="w-3 h-3 rounded-full bg-white opacity-50 slider-dot" data-index="2"></button>
            </div>
        </section>

        

        <!-- Suggested Products -->
        <section class="py-10 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[var(--dark)]">Gợi Ý Cho</h2>
                    <a href="quanao.php" class="text-[var(--primary)] font-medium hover:underline">Xem tất cả <i class="fas fa-chevron-right ml-1"></i></a>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <?php foreach ($random_products as $product): ?>
                        <div class="product-card">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="block relative">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300'); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-40 object-cover" 
                                     loading="lazy">
                               
                            </a>
                            <div class="p-3">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="block">
                                    <h3 class="text-sm font-medium mb-1 truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
                                </a>
                                <div class="flex items-center mb-1">
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="product-sold ml-1">(<?php echo rand(10, 200); ?>)</span>
                                </div>
                                <div class="product-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</div>
                               
                                <div class="flex justify-between items-center">
                        <button class="btn-add text-white px-4 py-2 rounded-full text-sm font-semibold transition">Thêm vào Giỏ</button>
                        <div class="flex gap-3">
                            <a class="text-gray-400 hover:text-pink-500 transition" href="#" title="Yêu Thích"><i class="fas fa-heart"></i></a>
                            <a class="text-gray-400 hover:text-pink-500 transition" href="product_detail_quanao.php?id=<?php echo $product['id']; ?>" title="Chi Tiết"><i class="fas fa-info-circle"></i></a>
                        </div>
                    </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Banner -->
        <section class="py-6 bg-white">
            <div class="container mx-auto px-4">
                <a href="#">
                    <img src="https://file.hstatic.net/1000384325/file/baner-chinh-khoacthethao-111_b58eca807d75439c807002eb210171d3.jpg" alt="Banner" class="w-full rounded-lg" loading="lazy">
                </a>
            </div>
        </section>

    

        <!-- Newsletter -->
        <section class="py-12 bg-[var(--dark)] text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Đăng Ký Nhận Tin</h2>
                <p class="mb-6 max-w-2xl mx-auto">Nhận thông báo về sản phẩm mới, khuyến mãi đặc biệt và nhiều hơn nữa!</p>
                <form class="flex flex-col sm:flex-row max-w-md mx-auto sm:max-w-xl gap-2">
                    <input type="email" placeholder="Nhập email của bạn" class="flex-grow px-4 py-3 rounded-md text-[var(--dark)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]">
                    <button type="submit" class="btn-secondary px-6 py-3 whitespace-nowrap">Đăng Ký</button>
                </form>
            </div>
        </section>
    </main>

        <!-- Footer -->
        <footer class="bg-[var(--dark)] py-12">
        <div class="container mx-auto px-4 text-white">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--primary)]">Liên Hệ</h3>
                    <p><i class="fas fa-phone mr-2"></i> +123 456 789</p>
                    <p><i class="fas fa-envelope mr-2"></i> info@3bropack.com</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> 123 Đường Thể Thao</p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--primary)]">Giờ Hoạt Động</h3>
                    <p><i class="fas fa-clock mr-2"></i> T2-T6: 9:00 - 20:00</p>
                    <p><i class="fas fa-clock mr-2"></i> T7: 10:00 - 18:00</p>
                    <p><i class="fas fa-clock mr-2"></i> CN: Đóng cửa</p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--primary)]">Chi Nhánh</h3>
                    <p><i class="fas fa-store mr-2"></i> 456 Đường Thể Thao</p>
                    <p><i class="fas fa-store mr-2"></i> 789 Đường Thể Thao</p>
                    <p><i class="fas fa-store mr-2"></i> 101 Đường Thể Thao</p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--primary)]">Dịch Vụ</h3>
                    <p><i class="fas fa-shipping-fast mr-2"></i> Giao hàng nhanh</p>
                    <p><i class="fas fa-undo mr-2"></i> Đổi trả 30 ngày</p>
                    <p><i class="fas fa-headset mr-2"></i> Hỗ trợ 24/7</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-[var(--primary)] text-white p-3 rounded-full shadow-lg opacity-0 invisible transition-all">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMobileMenu = document.getElementById('close-mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });
        
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
        
        // Hero Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = slides.length;
        
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }
        
        function nextSlide() {
            let newIndex = (currentSlide + 1) % totalSlides;
            showSlide(newIndex);
        }
        
        // Add click event to dots
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                showSlide(parseInt(dot.dataset.index));
            });
        });
        
        // Auto slide
        let slideInterval = setInterval(nextSlide, 5000);
        
        // Pause on hover
        const slider = document.querySelector('.hero-slider');
        slider.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });
        
        slider.addEventListener('mouseleave', () => {
            slideInterval = setInterval(nextSlide, 5000);
        });
        
        // Back to Top Button
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.remove('opacity-100', 'visible');
                backToTopButton.classList.add('opacity-0', 'invisible');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Countdown Timer for Flash Sale (example)
        function updateCountdown() {
            const countdownElement = document.querySelector('.countdown');
            if (countdownElement) {
                // This is just an example - in a real app you would calculate actual time remaining
                countdownElement.textContent = '11:23:45';
            }
        }
        
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
    
    <?php mysqli_close($conn); ?>
</body>
</html>
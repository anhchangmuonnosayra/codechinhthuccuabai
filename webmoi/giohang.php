<?php
session_start();
include 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - 3BROPACK</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        .header {
            background-color: #fff;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.05);
        }
        .cart-item {
            background: white;
            border-radius: 4px;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.05);
        }
        .btn-primary {
            background: #ee4d2d;
            color: white;
        }
        .btn-primary:hover {
            background: #e3381a;
        }
        .btn-outline {
            border: 1px solid #ee4d2d;
            color: #ee4d2d;
        }
        .btn-outline:hover {
            background: rgba(238,77,45,.08);
        }
        .quantity-btn {
            border: 1px solid #ccc;
            background: white;
            width: 32px;
            height: 32px;
        }
        .quantity-input {
            width: 50px;
            height: 32px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }
        .lazada-checkbox {
            width: 16px;
            height: 16px;
            border: 1px solid rgba(0,0,0,.14);
            border-radius: 2px;
        }
        .lazada-checkbox:checked {
            background-color: #ee4d2d;
            border-color: #ee4d2d;
        }
        .price {
            color: #ee4d2d;
        }
        .shipping-badge {
            background: #f5f5f5;
            border: 1px solid #e8e8e8;
            color: #666;
            font-size: 12px;
            padding: 2px 4px;
            border-radius: 2px;
        }
        .summary-card {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.05);
        }
        .promo-input {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 2px;
            width: 100%;
        }
        .seller-header {
            background: #fafafa;
            padding: 12px;
            border-radius: 4px 4px 0 0;
        }
        .free-shipping {
            color: #00ab56;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Header -->
    <div class="header py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center">
                <img alt="Logo của shop 3BROPACK" class="h-10 w-10 mr-3" src="3propack-removebg-preview.png"/>
                <span class="font-bold text-xl text-gray-800">3BROPACK</span>
                <div class="ml-8 flex items-center">
                    <span class="text-xl font-medium">Giỏ Hàng</span>
                    <div class="ml-4 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <span id="cart-count" class="text-sm">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Left Column - Cart Items -->
            <div class="lg:w-2/3">
                <!-- Cart Header -->
                <div class="bg-white p-4 rounded shadow-sm mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="select-all" class="lazada-checkbox mr-3">
                            <label for="select-all" class="cursor-pointer">Chọn tất cả</label>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-4">Thành tiền</span>
                            <span>Thao tác</span>
                        </div>
                    </div>
                </div>

                <!-- Seller Group -->
                <div class="seller-group mb-6">
                    
                    
                    <!-- Cart Items Container -->
                    <div id="cart-items" class="border border-t-0 rounded-b">
                        <!-- Cart items will be rendered here -->
                    </div>
                </div>

                <!-- Shipping Promotion -->
                <div class="summary-card p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="fas fa-truck text-gray-500 mr-2"></i>
                            <span class="text-sm">Nhập mã giảm giá hoặc Ưu đãi</span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-500"></i>
                    </div>
                    <div class="mt-3 hidden" id="promo-section">
                        <input type="text" placeholder="Nhập mã giảm giá" class="promo-input mb-2">
                        <button class="btn-outline px-4 py-2 rounded text-sm">Áp dụng</button>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:w-1/3">
                <div class="summary-card p-4 sticky top-4">
                    <div class="flex justify-between items-center border-b pb-3 mb-3">
                        <span>Tạm tính</span>
                        <span id="subtotal" class="price">0₫</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-3 mb-3">
                        <span>Giảm giá</span>
                        <span id="discount">0₫</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-3 mb-3">
                        <span>Phí vận chuyển</span>
                        <span id="shipping" class="free-shipping">10.000 vnđ</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 mb-3">
                        <span class="font-bold text-lg">Tổng tiền</span>
                        <span id="total" class="price text-2xl">0₫</span>
                    </div>
                    <button id="pay-btn" class="btn-primary w-full py-3 rounded font-medium mb-3">
                        Mua hàng (<span id="selected-count">0</span>)
                    </button>
                    <div class="text-xs text-gray-500">
                        Nhấn "Mua hàng" đồng nghĩa với việc bạn đồng ý tuân theo Điều khoản 3BROPACK
                    </div>
                </div>

                <!-- Security Info -->
                <div class="summary-card p-4 mt-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-shield-alt text-gray-500 mr-2"></i>
                        <span class="text-sm font-medium">Bảo mật giao dịch</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        <p class="mb-1"><i class="fas fa-check text-green-500 mr-1"></i> Cam kết hàng chính hãng 100%</p>
                        <p><i class="fas fa-check text-green-500 mr-1"></i> Đổi trả trong 7 ngày nếu không hài lòng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        const renderCart = () => {
            const cartItems = document.getElementById('cart-items');
            const cartCount = document.getElementById('cart-count');
            
            // Update cart count in header
            cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);

            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center py-10 bg-white">
                        <img src="empty-cart.png" alt="Empty cart" class="mx-auto h-32 mb-4">
                        <p class="text-gray-500 mb-4">Giỏ hàng của bạn còn trống</p>
                        <a href="index.php" class="btn-primary inline-block px-6 py-2 rounded">Mua ngay</a>
                    </div>
                `;
                return;
            }

            cartItems.innerHTML = cart.map((item, index) => `
                <div class="cart-item flex items-center p-4 border-b last:border-b-0">
                    <div class="flex items-center w-1/2">
                        <input type="checkbox" class="lazada-checkbox mr-3 select-item" data-index="${index}">
                        <img src="${decodeURIComponent(item.img)}" alt="${decodeURIComponent(item.name)}" class="w-16 h-16 object-cover mr-3">
                        <div>
                            <h3 class="font-medium text-sm">${decodeURIComponent(item.name)}</h3>
                            <div class="text-xs text-gray-500 mt-1">
                                <span class="shipping-badge">Yêu thích</span>
                                <span class="shipping-badge ml-1">Mới</span>
                            </div>
                            <p class="free-shipping text-xs mt-1"><i class="fas fa-shipping-fast mr-1"></i> Miễn phí vận chuyển</p>
                        </div>
                    </div>
                    <div class="w-1/6 text-right price text-sm">${parseFloat(item.price).toLocaleString('vi-VN')}₫</div>
                    <div class="w-1/6 flex justify-center">
                        <div class="flex items-center">
                            <button class="quantity-btn decrease-qty rounded-l" data-index="${index}">-</button>
                            <input type="text" value="${item.quantity}" class="quantity-input" data-index="${index}">
                            <button class="quantity-btn increase-qty rounded-r" data-index="${index}">+</button>
                        </div>
                    </div>
                    <div class="w-1/6 text-right price text-sm">${(parseFloat(item.price) * item.quantity).toLocaleString('vi-VN')}₫</div>
                    <div class="w-1/6 text-right">
                        <button class="delete-item text-gray-400 hover:text-red-500" data-index="${index}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            updateSummary();
        };

        const updateSummary = () => {
            const selectedItems = cart.filter((item, index) => 
                document.querySelector(`.select-item[data-index="${index}"]`)?.checked
            );
            
            const subtotal = selectedItems.reduce((sum, item) => 
                sum + (parseFloat(item.price) * item.quantity), 0
            );
            
            const total = subtotal;
            const selectedCount = selectedItems.reduce((sum, item) => sum + item.quantity, 0);

            document.getElementById('subtotal').textContent = `${subtotal.toLocaleString('vi-VN')}₫`;
            document.getElementById('total').textContent = `${total.toLocaleString('vi-VN')}₫`;
            document.getElementById('selected-count').textContent = selectedCount;
        };

        // Event listeners
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.select-item').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSummary();
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('select-item')) {
                updateSummary();
            }
            
            if (e.target.classList.contains('increase-qty')) {
                const index = e.target.dataset.index;
                cart[index].quantity += 1;
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            }
            
            if (e.target.classList.contains('decrease-qty')) {
                const index = e.target.dataset.index;
                if (cart[index].quantity > 1) {
                    cart[index].quantity -= 1;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCart();
                }
            }
            
            if (e.target.classList.contains('delete-item') || e.target.closest('.delete-item')) {
                const index = e.target.dataset.index || e.target.closest('.delete-item').dataset.index;
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            }
        });

        // Quantity input change handler
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const index = e.target.dataset.index;
                const newQuantity = parseInt(e.target.value) || 1;
                cart[index].quantity = newQuantity;
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            }
        });

        document.getElementById('pay-btn').addEventListener('click', () => {
            const selectedItems = cart.filter((item, index) => 
                document.querySelector(`.select-item[data-index="${index}"]`)?.checked
            );
            
            if (selectedItems.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
                return;
            }
            
            fetch('save_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ selectedItems })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'thanhtoan.php';
                } else {
                    alert('Có lỗi xảy ra khi lưu giỏ hàng.');
                }
            });
        });

        // Toggle promo section
        document.querySelector('.fa-chevron-down').addEventListener('click', function() {
            const promoSection = document.getElementById('promo-section');
            promoSection.classList.toggle('hidden');
            this.classList.toggle('fa-chevron-down');
            this.classList.toggle('fa-chevron-up');
        });

        // Initialize
        renderCart();
    </script>
</body>
</html>
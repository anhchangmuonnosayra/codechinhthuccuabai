<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Điều Hướng Thể Thao</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  intent="WELCOME"
  chat-title="Ai_Sports_Store"
  agent-id="14d0b1a0-93b8-4b95-bcc8-45cba8143752"
  language-code="vi"
></df-messenger>
    <style>
        body { background-color: #f0f0f0; margin: 0; }
        .navbar { height: 100vh; position: fixed; top: 0; left: 0; display: flex; align-items: center; padding: 20px; z-index: 1000; }
        .navbar ul { list-style: none; padding: 0; margin: 0; background: #37474f; box-shadow: -6px 12px 5px 2px rgba(145, 145, 145, 0.5); border-radius: 8px; }
        .navbar li { margin: 10px 0; }
        .vlink { color: #ffffff; position: relative; height: 54px; width: 72px; text-align: center; line-height: 54px; display: block; font-size: 1.2rem; text-decoration: none; border-radius: 6px; }
        .vlink span { position: absolute; opacity: 0; transition: transform 400ms ease-out; margin-left: 24px; box-shadow: 2px 4px 3px 1px rgba(0, 0, 0, 0.2); background: #424242; padding: 8px 12px; border-radius: 6px; font-size: 16px; white-space: nowrap; }
        .vlink span:before { content: ''; display: block; width: 0; height: 0; position: absolute; border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-right: 8px solid #424242; left: -12px; top: 50%; transform: translateY(-50%); }
        .vlink:hover { background: #4fc3f7; box-shadow: 1px 2px 3px #9d451d; }
        .vlink:hover span { opacity: 1; transform: translateX(12px); }
        .vlink i { margin-right: 8px; }
        .content-wrapper { margin-left: 100px; }
    </style>
</head>
<body>
    <div class="navbar">
        <ul>
            <li><a class="vlink rounded border-0" href="index.php"><i class="fas fa-home"></i> <span>Trang Chủ</span></a></li>
            <li><a class="vlink rounded" href="quanao.php"><i class="fas fa-tshirt"></i> <span>Quần Áo</span></a></li>
            <li><a class="vlink rounded" href="giay.php"><i class="fas fa-shoe-prints"></i> <span>Giày Thể Thao</span></a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a class="vlink rounded" href="taikhoan.php"><i class="fas fa-user"></i> <span>Tài Khoản</span></a></li>
                <li><a class="vlink rounded" href="giohang.php"><i class="fas fa-shopping-cart"></i> <span>Giỏ Hàng</span></a></li>
                <li><a class="vlink rounded" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Đăng Xuất</span></a></li>
            <?php else: ?>
                <li><a class="vlink rounded" href="taikhoan.php"><i class="fas fa-user"></i> <span>Tài Khoản</span></a></li>
                <li><a class="vlink rounded" href="giohang.php"><i class="fas fa-shopping-cart"></i> <span>Giỏ Hàng</span></a></li>
                <li><a class="vlink rounded" href="login.php"><i class="fas fa-sign-in-alt"></i> <span>Đăng Nhập</span></a></li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
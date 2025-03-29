<?php
ob_start();
session_start();
include 'db_connect.php';

// Xử lý đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            mysqli_close($conn);
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Mật khẩu không đúng!";
        }
    } else {
        $login_error = "Email không tồn tại!";
    }
}

// Xử lý đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['userId']);
    $email = mysqli_real_escape_string($conn, $_POST['signUpEmail']);
    $password = password_hash($_POST['signUpPassword'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
    
    if (mysqli_query($conn, $sql)) {
        $signup_success = "Đăng ký thành công! Vui lòng đăng nhập.";
    } else {
        $signup_error = "Lỗi: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập / Đăng Ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Thêm SDK Facebook JavaScript -->
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v12.0&appId=YOUR_FACEBOOK_APP_ID&autoLogAppEvents=1" nonce="YOUR_NONCE"></script>
<!-- Thêm Google API Client -->
<meta name="google-signin-client_id" content="398276343567-sq76i5iao3tvct8k3lcdoubkamd8ichm.apps.googleusercontent.com">
<script src="https://accounts.google.com/gsi/client" async defer></script>
    <meta name="google-signin-client_id" content="YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com">
    <script>
        function toggleForm() {
            const loginForm = document.getElementById('loginForm');
            const signUpForm = document.getElementById('signUpForm');
            loginForm.classList.toggle('hidden');
            signUpForm.classList.toggle('hidden');
        }
        function handleGoogleSignIn(response) {
    // Gửi token ID đến server để xác thực
    const id_token = response.credential;
    
    // Tạo form ẩn để gửi dữ liệu
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'google_login.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id_token';
    input.value = id_token;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
        // Xử lý đăng nhập Google
        function onGoogleSignIn(googleUser) {
            const profile = googleUser.getBasicProfile();
            console.log('ID: ' + profile.getId());
            console.log('Name: ' + profile.getName());
            console.log('Email: ' + profile.getEmail());
            
            // Gửi thông tin về server để xử lý
            window.location.href = 'google_login.php?token=' + googleUser.getAuthResponse().id_token;
        }

        // Xử lý đăng nhập Facebook
        function checkLoginState() {
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    // Người dùng đã đăng nhập và ủy quyền ứng dụng
                    window.location.href = 'facebook_login.php?token=' + response.authResponse.accessToken;
                }
            });
        }

        // Khởi tạo Facebook SDK
        window.fbAsyncInit = function() {
            FB.init({
                appId      : 'YOUR_FACEBOOK_APP_ID',
                cookie     : true,
                xfbml      : true,
                version    : 'v12.0'
            });
        };
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 8px rgba(255, 107, 107, 0.3);
        }
        .btn-primary {
            background: linear-gradient(90deg, #ff6b6b 0%, #ff8e53 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        }
        .btn-social {
            transition: all 0.3s ease;
        }
        .btn-social:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <?php include 'navbar.php'; ?>

    <div class="form-container rounded-2xl overflow-hidden w-full max-w-4xl flex">
        <!-- Form Section -->
        <div class="w-1/2 p-8">
            <!-- Login Form -->
            <div id="loginForm">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-6">Đăng Nhập</h2>
                <div class="flex justify-center gap-4 mb-6">
    <!-- Nút đăng nhập Google -->
    <div id="g_id_onload"
         data-client_id="398276343567-sq76i5iao3tvct8k3lcdoubkamd8ichm.apps.googleusercontent.com"
         data-context="signin"
         data-ux_mode="popup"
         data-callback="handleGoogleSignIn"
         data-auto_prompt="false">
    </div>

    <div class="g_id_signin"
         data-type="standard"
         data-shape="pill"
         data-theme="filled_blue"
         data-text="signin_with"
         data-size="large"
         data-logo_alignment="left">
    </div>
    
    <!-- Nút đăng nhập Facebook (giữ nguyên) -->
    <div class="fb-login-button" 
         data-size="large" 
         data-button-type="login_with" 
         data-layout="default" 
         data-auto-logout-link="false" 
         data-use-continue-as="false"
         data-scope="public_profile,email"
         onlogin="checkLoginState();">
    </div>
</div>
                <p class="text-center text-gray-600 mb-6">hoặc dùng Email của bạn</p>
                <?php if (isset($login_error)): ?>
                    <p class="text-red-500 text-center mb-4"><?php echo $login_error; ?></p>
                <?php endif; ?>
                <?php if (isset($signup_success)): ?>
                    <p class="text-green-500 text-center mb-4"><?php echo $signup_success; ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="login" value="1">
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="email">Email</label>
                        <input class="input-field w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none" type="email" id="email" name="email" required>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="password">Mật Khẩu</label>
                        <input class="input-field w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none" type="password" id="password" name="password" required>
                    </div>
                    <div class="mb-6 text-right">
                        <a href="#" class="text-sm text-blue-500 hover:underline">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btn-primary w-full py-3 rounded-full text-white font-semibold">ĐĂNG NHẬP</button>
                </form>
            </div>

            <!-- Signup Form -->
            <div id="signUpForm" class="hidden">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-6">Đăng Ký</h2>
                <?php if (isset($signup_error)): ?>
                    <p class="text-red-500 text-center mb-4"><?php echo $signup_error; ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="signup" value="1">
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="userId">Tên Người Dùng</label>
                        <input class="input-field w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none" type="text" id="userId" name="userId" required>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="signUpEmail">Email</label>
                        <input class="input-field w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none" type="email" id="signUpEmail" name="signUpEmail" required>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="signUpPassword">Mật Khẩu</label>
                        <input class="input-field w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none" type="password" id="signUpPassword" name="signUpPassword" required>
                    </div>
                    <div class="mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox text-red-500 rounded" required>
                            <span class="ml-2 text-gray-700">Tôi đồng ý với thông tin trên</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full py-3 rounded-full text-white font-semibold">ĐĂNG KÝ</button>
                </form>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="w-1/2 p-8 bg-gradient-to-br from-[#ff6b6b] to-[#ff8e53] text-white flex flex-col items-center justify-center">
            <h2 class="text-3xl font-extrabold mb-6">Xin Chào!</h2>
            <p class="text-center mb-6 text-lg">Chưa có tài khoản? Đăng ký ngay để trải nghiệm!</p>
            <button onclick="toggleForm()" class="py-3 px-8 rounded-full bg-white text-[#ff6b6b] font-semibold hover:bg-opacity-90 transition">ĐĂNG KÝ</button>
        </div>
    </div>
</body>
</html>
<?php
session_start();
include 'db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id == 0) {
    header("Location: admin_add_product.php");
    exit();
}

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM product WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);
if (!$product) {
    header("Location: admin_add_product.php");
    exit();
}

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $price = floatval($_POST['price']);
    $category = htmlspecialchars($_POST['category']);
    $size = intval($_POST['size']);
    
    $image_url = $product['image_url'];
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false && $_FILES["image"]["size"] <= 5000000 && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                if (file_exists($image_url)) {
                    unlink($image_url);
                }
                $image_url = $target_file;
            } else {
                $error = "Lỗi khi tải ảnh lên.";
            }
        } else {
            $error = "Ảnh không hợp lệ (chỉ JPG, JPEG, PNG, GIF, tối đa 5MB).";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE product SET name = ?, price = ?, category = ?, size = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("sdsssi", $name, $price, $category, $size, $image_url, $product_id);
        if ($stmt->execute()) {
            $message = "Cập nhật sản phẩm thành công!";
            $result = mysqli_query($conn, $sql);
            $product = mysqli_fetch_assoc($result);
        } else {
            $error = "Lỗi khi cập nhật sản phẩm: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sản Phẩm - 3BROPACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
</head>
<body class="font-roboto bg-gray-100">
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Chỉnh Sửa Sản Phẩm</h1>

    <?php if (isset($message)): ?>
        <div class="bg-green-500 text-white p-4 rounded mb-6"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="bg-red-500 text-white p-4 rounded mb-6"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="name">Tên Sản Phẩm</label>
                <input class="w-full border border-gray-300 p-2 rounded" id="name" name="name" required type="text" value="<?php echo htmlspecialchars($product['name']); ?>"/>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="price">Giá (VNĐ)</label>
                <input class="w-full border border-gray-300 p-2 rounded" id="price" name="price" required type="number" step="0.01" value="<?php echo $product['price']; ?>"/>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="category">Danh Mục</label>
                <select class="w-full border border-gray-300 p-2 rounded" id="category" name="category" required>
                    <option value="walking" <?php echo $product['category'] == 'walking' ? 'selected' : ''; ?>>Giày Thể Thao Đi Bộ</option>
                    <option value="badminton" <?php echo $product['category'] == 'badminton' ? 'selected' : ''; ?>>Giày Cầu Lông</option>
                    <option value="volleyball" <?php echo $product['category'] == 'volleyball' ? 'selected' : ''; ?>>Giày Bóng Chuyền</option>
                    <option value="artificial-turf" <?php echo $product['category'] == 'artificial-turf' ? 'selected' : ''; ?>>Giày Sân Nhân Tạo</option>
                    <option value="natural-grass" <?php echo $product['category'] == 'natural-grass' ? 'selected' : ''; ?>>Giày Cỏ Tự Nhiên</option>
                    <option value="futsal" <?php echo $product['category'] == 'futsal' ? 'selected' : ''; ?>>Giày Fusal</option>
                    <option value="adult" <?php echo $product['category'] == 'adult' ? 'selected' : ''; ?>>Giày Người Lớn</option>
                    <option value="children" <?php echo $product['category'] == 'children' ? 'selected' : ''; ?>>Giày Trẻ Em</option>
                    <option value="jacket" <?php echo $product['category'] == 'jacket' ? 'selected' : ''; ?>>Áo Khoác</option>
                    <option value="dress" <?php echo $product['category'] == 'dress' ? 'selected' : ''; ?>>Đầm Nữ</option>
                    <option value="t-shirt" <?php echo $product['category'] == 't-shirt' ? 'selected' : ''; ?>>Áo Thun</option>
                    <option value="bag" <?php echo $product['category'] == 'bag' ? 'selected' : ''; ?>>Túi Xách</option>
                    <option value="shirt" <?php echo $product['category'] == 'shirt' ? 'selected' : ''; ?>>Áo Sơ Mi</option>
                    <option value="shorts" <?php echo $product['category'] == 'shorts' ? 'selected' : ''; ?>>Quần Short</option>
                    <option value="accessory" <?php echo $product['category'] == 'accessory' ? 'selected' : ''; ?>>Phụ Kiện</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="size">Size</label>
                <input class="w-full border border-gray-300 p-2 rounded" id="size" name="size" required type="number" value="<?php echo $product['size']; ?>"/>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="image">Hình Ảnh (Để trống nếu không thay đổi)</label>
                <input class="w-full border border-gray-300 p-2 rounded" id="image" name="image" type="file" accept="image/*"/>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Ảnh hiện tại" class="w-32 h-32 object-cover mt-2">
            </div>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" type="submit">Cập Nhật Sản Phẩm</button>
            <a href="admin_add_product.php" class="ml-4 text-gray-600 hover:underline">Quay lại</a>
        </form>
    </div>
</div>

<?php mysqli_close($conn); ?>
</body>
</html>
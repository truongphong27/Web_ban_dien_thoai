<?php
include_once '../config/db.php';

// Lấy ID sản phẩm cần chỉnh sửa
$id = $_GET['id'];

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$sql = "SELECT * FROM san_pham WHERE id = $id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

// Lấy thông tin hình ảnh của sản phẩm
$sql_images = "SELECT * FROM hinh_anh_san_pham WHERE san_pham_id = $id";
$result_images = $conn->query($sql_images);

// Cập nhật thông tin sản phẩm khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_san_pham = $_POST['ten_san_pham'];
    $gia = $_POST['gia'];
    $so_luong = $_POST['so_luong'];
    $loai_id = $_POST['loai_id'];
    $mo_ta = $_POST['mo_ta'];

    // Cập nhật sản phẩm vào bảng san_pham
    $sql_update = "UPDATE san_pham SET 
                    ten_san_pham = '$ten_san_pham',
                    gia = '$gia',
                    so_luong = '$so_luong',
                    loai_id = '$loai_id',
                    mo_ta = '$mo_ta'
                   WHERE id = $id";
    $conn->query($sql_update);

    // Xử lý hình ảnh (cho phép nhiều file)
    if (isset($_FILES['hinh_anh']['name'])) {
        $total_files = count($_FILES['hinh_anh']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $target_dir = "images/";  // Thư mục lưu trữ ảnh
            $target_file = $target_dir . basename($_FILES["hinh_anh"]["name"][$i]);

            // Kiểm tra xem tệp có phải là ảnh và có thể tải lên không
            if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"][$i], $target_file)) {
                // Lấy mô tả ngắn cho hình ảnh
                $mo_ta_ngan_gon = isset($_POST['mo_ta_hinh'][$i]) ? $_POST['mo_ta_hinh'][$i] : '';

                // Thêm hình ảnh vào bảng hinh_anh_san_pham và liên kết với sản phẩm
                $sql_image = "INSERT INTO hinh_anh_san_pham (san_pham_id, ten_file, mo_ta_ngan) 
                              VALUES ($id, '$target_file', '$mo_ta_ngan_gon')";
                $conn->query($sql_image);
            }
        }
    }

    // Cập nhật mô tả hình ảnh đã có
    if (isset($_POST['mo_ta_hinh_old'])) {
        $mo_ta_hinh_old = $_POST['mo_ta_hinh_old'];
        foreach ($mo_ta_hinh_old as $image_id => $mo_ta) {
            $sql_update_image = "UPDATE hinh_anh_san_pham SET mo_ta_ngan_gon = '$mo_ta' WHERE id = $image_id";
            $conn->query($sql_update_image);
        }
    }

    // Quay lại danh sách sản phẩm
    header('Location: index.php');
    exit();
}

// Xóa hình ảnh
if (isset($_GET['delete_image_id'])) {
    $delete_image_id = $_GET['delete_image_id'];
    $sql_delete_image = "DELETE FROM hinh_anh_san_pham WHERE id = $delete_image_id";
    $conn->query($sql_delete_image);
    header("Location: edit.php?id=$id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    .image-group {
        margin-bottom: 15px;
    }

    .image-group input[type="file"] {
        display: inline-block;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Điện thoại Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Danh sách sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sửa sản phẩm -->
    <div class="container my-5">
        <h2 class="text-center">Sửa sản phẩm</h2>
        <form method="POST" enctype="multipart/form-data" id="edit_product_form">
            <div class="mb-3">
                <label for="ten_san_pham" class="form-label">Tên sản phẩm</label>
                <input type="text" class="form-control" id="ten_san_pham" name="ten_san_pham"
                    value="<?= $product['ten_san_pham'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="gia" class="form-label">Giá</label>
                <input type="number" class="form-control" id="gia" name="gia" value="<?= $product['gia'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="so_luong" class="form-label">Số lượng</label>
                <input type="number" class="form-control" id="so_luong" name="so_luong"
                    value="<?= $product['so_luong'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="loai_id" class="form-label">Loại sản phẩm</label>
                <input type="number" class="form-control" id="loai_id" name="loai_id" value="<?= $product['loai_id'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả sản phẩm</label>
                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"
                    required><?= $product['mo_ta'] ?></textarea>
            </div>

            <!-- Hiển thị các hình ảnh hiện tại của sản phẩm và chỉnh sửa mô tả -->
            <h5>Hình ảnh hiện tại</h5>
            <div id="current-images">
                <?php while ($image = $result_images->fetch_assoc()): ?>
                <div class="image-group">
                    <img src="<?= $image['ten_file'] ?>" width="100" height="100" class="m-1">
                    <input type="text" class="form-control mt-2" name="mo_ta_hinh_old[<?= $image['id'] ?>]"
                        value="<?= $image['mo_ta_ngan'] ?>" placeholder="Mô tả ngắn cho hình ảnh">
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        onclick="location.href='edit.php?id=<?= $id ?>&delete_image_id=<?= $image['id'] ?>'">Xóa
                        ảnh</button>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Nhóm hình ảnh mới -->
            <div id="image-upload-section">
                <div class="image-group">
                    <label for="hinh_anh[]" class="form-label">Hình ảnh sản phẩm</label>
                    <input type="file" class="form-control" name="hinh_anh[]" accept="image/*">
                    <input type="text" class="form-control mt-2" name="mo_ta_hinh[]"
                        placeholder="Mô tả ngắn cho hình ảnh">
                </div>
            </div>

            <!-- Thêm ảnh -->
            <button type="button" class="btn btn-secondary" id="add-image-btn">Thêm ảnh</button>

            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-3">Cập nhật sản phẩm</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Thêm nhóm hình ảnh khi nhấn nút "Thêm ảnh"
    document.getElementById('add-image-btn').addEventListener('click', function() {
        const newImageGroup = document.createElement('div');
        newImageGroup.classList.add('image-group');
        newImageGroup.innerHTML = `
                <label for="hinh_anh[]" class="form-label">Hình ảnh sản phẩm</label>
                <input type="file" class="form-control" name="hinh_anh[]" accept="image/*">
                <input type="text" class="form-control mt-2" name="mo_ta_hinh[]" placeholder="Mô tả ngắn cho hình ảnh">
            `;
        document.getElementById('image-upload-section').appendChild(newImageGroup);
    });
    </script>
</body>

</html>
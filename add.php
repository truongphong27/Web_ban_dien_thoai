<?php
include_once '../config/db.php';
include 'header.html';
// Lấy ID admin từ phiên đăng nhập (cần đảm bảo admin đã đăng nhập)
session_start();
$tao_boi_admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1;  // Nếu không có session thì gán mặc định là 1

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ma_san_pham = $_POST['ma_san_pham'];
    $ten_san_pham = $_POST['ten_san_pham'];
    $gia = $_POST['gia'];
    $so_luong = $_POST['so_luong'];
    $loai_id = $_POST['loai_id'];
    $mo_ta = $_POST['mo_ta'];
    $dung_luong = $_POST['dung_luong'];
    $mau_sac = $_POST['mau_sac'];
    $ma_mau = $_POST['ma_mau'];
    $phan_tram_giam = $_POST['phan_tram_giam']; // Lấy phần trăm giảm từ form

    // Thêm sản phẩm vào bảng san_pham
    $sql_insert = "INSERT INTO san_pham (ma_san_pham, ten_san_pham, gia, so_luong, loai_id, mo_ta, dung_luong, mau_sac, ma_mau, phan_tram_giam, tao_boi_admin_id) 
                   VALUES ('$ma_san_pham', '$ten_san_pham', '$gia', '$so_luong', '$loai_id', '$mo_ta', '$dung_luong', '$mau_sac', '$ma_mau', '$phan_tram_giam', '$tao_boi_admin_id')";

    if ($conn->query($sql_insert) === TRUE) {
        $last_id = $conn->insert_id;  // Lấy ID của sản phẩm vừa thêm

        // Xử lý hình ảnh (cho phép nhiều file)
        if (isset($_FILES['hinh_anh']['name'])) {
            $total_files = count($_FILES['hinh_anh']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                // Lưu hình ảnh vào thư mục images/
                $target_dir = "../img/";  // Thư mục lưu trữ ảnh
                $target_file = $target_dir . basename($_FILES["hinh_anh"]["name"][$i]);

                // Kiểm tra xem tệp có phải là ảnh và có thể tải lên không
                if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"][$i], $target_file)) {
                    // Lấy mô tả ngắn cho hình ảnh
                    $mo_ta_ngan_gon = isset($_POST['mo_ta_hinh'][$i]) ? $_POST['mo_ta_hinh'][$i] : '';

                    // Thêm hình ảnh vào bảng hinh_anh_san_pham và liên kết với sản phẩm
                    $sql_image = "INSERT INTO hinh_anh_san_pham (san_pham_id, ten_file, mo_ta_ngan) 
                                  VALUES ($last_id, '$target_file', '$mo_ta_ngan_gon')";
                    $conn->query($sql_image);
                }
            }
        }

        // Quay lại danh sách sản phẩm
        header('Location: index.php');
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Lấy tất cả loại sản phẩm từ cơ sở dữ liệu để hiển thị trong dropdown
$sql_categories = "SELECT * FROM loai_san_pham";
$categories_result = $conn->query($sql_categories);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thêm sản phẩm</title>
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


    <!-- Thêm sản phẩm -->
    <div class="container my-5">
        <h2 class="text-center">Thêm sản phẩm mới</h2>
        <form method="POST" enctype="multipart/form-data" id="add_product_form">
            <div class="mb-3">
                <label for="ma_san_pham" class="form-label">Mã sản phẩm</label>
                <input type="text" class="form-control" id="ma_san_pham" name="ma_san_pham" required>
            </div>
            <div class="mb-3">
                <label for="ten_san_pham" class="form-label">Tên sản phẩm</label>
                <input type="text" class="form-control" id="ten_san_pham" name="ten_san_pham" required>
            </div>
            <div class="mb-3">
                <label for="gia" class="form-label">Giá</label>
                <input type="number" class="form-control" id="gia" name="gia" required>
            </div>
            <div class="mb-3">
                <label for="so_luong" class="form-label">Số lượng</label>
                <input type="number" class="form-control" id="so_luong" name="so_luong" required>
            </div>
            <div class="mb-3">
                <label for="loai_id" class="form-label">Loại sản phẩm</label>
                <select class="form-control" name="loai_id" id="loai_id" required>
                    <option value="">Chọn loại sản phẩm</option>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?= $category['id'] ?>"><?= $category['ten_loai'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả sản phẩm</label>
                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="dung_luong" class="form-label">Dung lượng</label>
                <input type="text" class="form-control" id="dung_luong" name="dung_luong" required>
            </div>
            <div class="mb-3">
                <label for="mau_sac" class="form-label">Màu sắc</label>
                <input type="text" class="form-control" id="mau_sac" name="mau_sac" required>
            </div>
            <div class="mb-3">
                <label for="ma_mau" class="form-label">Mã màu</label>
                <input type="text" class="form-control" id="ma_mau" name="ma_mau" required>
            </div>
            <div class="mb-3">
                <label for="phan_tram_giam" class="form-label">Phần trăm giảm</label>
                <input type="number" class="form-control" id="phan_tram_giam" name="phan_tram_giam" required>
            </div>

            <!-- Nhóm hình ảnh và mô tả -->
            <div id="image-upload-section">
                <div class="image-group">
                    <label for="hinh_anh[]" class="form-label">Hình ảnh sản phẩm</label>
                    <input type="file" class="form-control" name="hinh_anh[]" accept="image/*" required>
                    <input type="text" class="form-control mt-2" name="mo_ta_hinh[]"
                        placeholder="Mô tả ngắn cho hình ảnh" required>
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-image-btn">Xóa ảnh</button>
                </div>
            </div>

            <!-- Thêm ảnh -->
            <button type="button" class="btn btn-secondary" id="add-image-btn">Thêm ảnh</button>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
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
                <input type="file" class="form-control" name="hinh_anh[]" accept="image/*" required>
                <input type="text" class="form-control mt-2" name="mo_ta_hinh[]" placeholder="Mô tả ngắn cho hình ảnh" required>
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-image-btn">Xóa ảnh</button>
            `;
        document.getElementById('image-upload-section').appendChild(newImageGroup);
    });

    // Xóa nhóm hình ảnh khi nhấn "Xóa ảnh"
    document.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('remove-image-btn')) {
            event.target.closest('.image-group').remove();
        }
    });
    </script>
</body>

</html>
<?php
include '../config/db.php';
include 'header.html';
// Lấy ID địa chỉ từ URL
$address_id = $_GET['id'];

// Truy vấn thông tin địa chỉ giao hàng
$sql = "SELECT * FROM dia_chi_giao_hang WHERE id = $address_id";
$result = $conn->query($sql);
$address = $result->fetch_assoc();

// Cập nhật thông tin địa chỉ khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_nguoi_nhan = $_POST['ten_nguoi_nhan'];
    $dia_chi = $_POST['dia_chi'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    
    // Cập nhật địa chỉ giao hàng
    $sql_update = "UPDATE dia_chi_giao_hang SET 
                    ten_nguoi_nhan = '$ten_nguoi_nhan',
                    dia_chi = '$dia_chi',
                    so_dien_thoai = '$so_dien_thoai'
                   WHERE id = $address_id";
    $conn->query($sql_update);

    // Quay lại danh sách địa chỉ giao hàng
    header("Location: view_address.php?id=" . $address['khach_hang_id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sửa Địa chỉ giao hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Sửa Địa chỉ giao hàng</h2>

        <form method="POST">
            <div class="mb-3">
                <label for="ten_nguoi_nhan" class="form-label">Tên người nhận</label>
                <input type="text" class="form-control" id="ten_nguoi_nhan" name="ten_nguoi_nhan"
                    value="<?= $address['ten_nguoi_nhan'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="dia_chi" class="form-label">Địa chỉ</label>
                <textarea class="form-control" id="dia_chi" name="dia_chi"
                    required><?= $address['dia_chi'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai"
                    value="<?= $address['so_dien_thoai'] ?>" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Cập nhật địa chỉ giao hàng</button>
        </form>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
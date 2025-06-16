<?php
include '../config/db.php';
include 'header.html';
// Lấy ID bảo hành từ URL
$id = $_GET['id'];

// Kiểm tra nếu người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $chi_tiet_don_hang_id = $_POST['chi_tiet_don_hang_id'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
    $trang_thai = $_POST['trang_thai'];
    $ghi_chu = $_POST['ghi_chu'];

    // Câu lệnh SQL để cập nhật bảo hành
    $sql = "UPDATE bao_hanh 
            SET chi_tiet_don_hang_id = '$chi_tiet_don_hang_id', ngay_bat_dau = '$ngay_bat_dau', 
                ngay_ket_thuc = '$ngay_ket_thuc', trang_thai = '$trang_thai', ghi_chu = '$ghi_chu' 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Cập nhật bảo hành thành công!'); window.location.href='manage_warranty.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Lấy dữ liệu bảo hành hiện tại
$sql = "SELECT * FROM bao_hanh WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Lấy danh sách đơn hàng để chọn trong form
$order_sql = "SELECT * FROM chi_tiet_don_hang";
$order_result = $conn->query($order_sql);

// Lấy trạng thái bảo hành
$status_list = [
    'con_han' => 'Còn hạn',
    'het_han' => 'Hết hạn',
    'da_bao_hanh' => 'Đã bảo hành'
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa Bảo Hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Sửa Bảo Hành</h2>
        <form method="POST">
            <!-- Chọn đơn hàng -->
            <div class="mb-3">
                <label for="chi_tiet_don_hang_id" class="form-label">Đơn hàng</label>
                <select class="form-control" id="chi_tiet_don_hang_id" name="chi_tiet_don_hang_id" required>
                    <?php while ($order_row = $order_result->fetch_assoc()): ?>
                    <option value="<?= $order_row['id'] ?>"
                        <?= $row['chi_tiet_don_hang_id'] == $order_row['id'] ? 'selected' : '' ?>>
                        Đơn hàng ID: <?= $order_row['id'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Ngày bắt đầu -->
            <div class="mb-3">
                <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu</label>
                <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau"
                    value="<?= $row['ngay_bat_dau'] ?>" required>
            </div>

            <!-- Ngày kết thúc -->
            <div class="mb-3">
                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc"
                    value="<?= $row['ngay_ket_thuc'] ?>" required>
            </div>

            <!-- Trạng thái bảo hành -->
            <div class="mb-3">
                <label for="trang_thai" class="form-label">Trạng thái</label>
                <select class="form-control" id="trang_thai" name="trang_thai" required>
                    <?php foreach ($status_list as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $row['trang_thai'] == $key ? 'selected' : '' ?>><?= $value ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ghi chú -->
            <div class="mb-3">
                <label for="ghi_chu" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="4"><?= $row['ghi_chu'] ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật bảo hành</button>
        </form>
    </div>
</body>

</html>
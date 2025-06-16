<?php
include '../config/db.php';
include 'header.html';
// Lấy mã giảm giá từ URL
$id = $_GET['id'];

// Kiểm tra nếu người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma_code = $_POST['ma_code'];
    $mo_ta = $_POST['mo_ta'];
    $giam_phan_tram = $_POST['giam_phan_tram'];
    $gia_tri_toi_thieu = $_POST['gia_tri_toi_thieu'];
    $so_lan_su_dung = $_POST['so_lan_su_dung'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
    $trang_thai = $_POST['trang_thai'];

    // Cập nhật mã giảm giá
    $sql = "UPDATE ma_giam_gia 
            SET ma_code='$ma_code', mo_ta='$mo_ta', giam_phan_tram='$giam_phan_tram', 
                gia_tri_toi_thieu='$gia_tri_toi_thieu', so_lan_su_dung='$so_lan_su_dung', 
                ngay_bat_dau='$ngay_bat_dau', ngay_ket_thuc='$ngay_ket_thuc', trang_thai='$trang_thai' 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Cập nhật mã giảm giá thành công!'); window.location.href='manage_discounts.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Lấy dữ liệu mã giảm giá hiện tại
$sql = "SELECT * FROM ma_giam_gia WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa Mã Giảm Giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Sửa Mã Giảm Giá</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="ma_code" class="form-label">Mã Giảm Giá</label>
                <input type="text" class="form-control" id="ma_code" name="ma_code" value="<?= $row['ma_code'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô Tả</label>
                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"><?= $row['mo_ta'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="giam_phan_tram" class="form-label">Giảm (%)</label>
                <input type="number" class="form-control" id="giam_phan_tram" name="giam_phan_tram"
                    value="<?= $row['giam_phan_tram'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="gia_tri_toi_thieu" class="form-label">Giá trị tối thiểu</label>
                <input type="number" class="form-control" id="gia_tri_toi_thieu" name="gia_tri_toi_thieu"
                    value="<?= $row['gia_tri_toi_thieu'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="so_lan_su_dung" class="form-label">Số lần sử dụng</label>
                <input type="number" class="form-control" id="so_lan_su_dung" name="so_lan_su_dung"
                    value="<?= $row['so_lan_su_dung'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu</label>
                <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau"
                    value="<?= $row['ngay_bat_dau'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc"
                    value="<?= $row['ngay_ket_thuc'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="trang_thai" class="form-label">Trạng thái</label>
                <select class="form-control" id="trang_thai" name="trang_thai" required>
                    <option value="kich_hoat" <?= $row['trang_thai'] == 'kich_hoat' ? 'selected' : '' ?>>Kích hoạt
                    </option>
                    <option value="het_hieu_luc" <?= $row['trang_thai'] == 'het_hieu_luc' ? 'selected' : '' ?>>Hết hiệu
                        lực</option>
                    <option value="tam_khoa" <?= $row['trang_thai'] == 'tam_khoa' ? 'selected' : '' ?>>Tạm khóa</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật mã giảm giá</button>
        </form>
    </div>
</body>

</html>
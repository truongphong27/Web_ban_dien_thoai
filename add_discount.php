<?php
include '../config/db.php';
include 'header.html';
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

    // Câu lệnh SQL để thêm mã giảm giá
    $sql = "INSERT INTO ma_giam_gia (ma_code, mo_ta, giam_phan_tram, gia_tri_toi_thieu, so_lan_su_dung, da_su_dung, ngay_bat_dau, ngay_ket_thuc, trang_thai)
            VALUES ('$ma_code', '$mo_ta', '$giam_phan_tram', '$gia_tri_toi_thieu', '$so_lan_su_dung', 0, '$ngay_bat_dau', '$ngay_ket_thuc', '$trang_thai')";

    // Thực thi câu lệnh
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm mã giảm giá thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Mã Giảm Giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Thêm Mã Giảm Giá</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="ma_code" class="form-label">Mã Giảm Giá</label>
                <input type="text" class="form-control" id="ma_code" name="ma_code" required>
            </div>
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô Tả</label>
                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="giam_phan_tram" class="form-label">Giảm (%)</label>
                <input type="number" class="form-control" id="giam_phan_tram" name="giam_phan_tram" required>
            </div>
            <div class="mb-3">
                <label for="gia_tri_toi_thieu" class="form-label">Giá trị tối thiểu</label>
                <input type="number" class="form-control" id="gia_tri_toi_thieu" name="gia_tri_toi_thieu" required>
            </div>
            <div class="mb-3">
                <label for="so_lan_su_dung" class="form-label">Số lần sử dụng</label>
                <input type="number" class="form-control" id="so_lan_su_dung" name="so_lan_su_dung" required>
            </div>
            <div class="mb-3">
                <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu</label>
                <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required>
            </div>
            <div class="mb-3">
                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
            </div>
            <div class="mb-3">
                <label for="trang_thai" class="form-label">Trạng thái</label>
                <select class="form-control" id="trang_thai" name="trang_thai" required>
                    <option value="kich_hoat">Kích hoạt</option>
                    <option value="het_hieu_luc">Hết hiệu lực</option>
                    <option value="tam_khoa">Tạm khóa</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Thêm mã giảm giá</button>
        </form>
    </div>
</body>

</html>
<?php
include '../config/db.php';
include 'header.html';

// Xử lý form thêm nhân viên
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ho_ten = $_POST['ho_ten'];
    $email_cong_ty = $_POST['email_cong_ty'];
    $sdt = $_POST['sdt'];
    $mat_khau = sha1($_POST['mat_khau']);  // Mã hóa mật khẩu bằng SHA1
    $loai_nhan_vien = $_POST['loai_nhan_vien'];
    
    // Thêm nhân viên vào bảng nhan_vien
    $sql_insert = "INSERT INTO nhan_vien (ho_ten, email_cong_ty, sdt, mat_khau, loai_nhan_vien) 
                   VALUES ('$ho_ten', '$email_cong_ty', '$sdt', '$mat_khau', '$loai_nhan_vien')";
    if ($conn->query($sql_insert) === TRUE) {
        // Quay lại danh sách nhân viên
        header('Location: index.php');
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thêm Nhân viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <!-- Thêm nhân viên -->
    <div class="container my-5">
        <h2 class="text-center">Thêm Nhân viên</h2>

        <form method="POST" action="add_employee.php">
            <div class="mb-3">
                <label for="ho_ten" class="form-label">Tên nhân viên</label>
                <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
            </div>

            <div class="mb-3">
                <label for="email_cong_ty" class="form-label">Email công ty</label>
                <input type="email" class="form-control" id="email_cong_ty" name="email_cong_ty" required>
            </div>

            <div class="mb-3">
                <label for="sdt" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="sdt" name="sdt" required>
            </div>

            <div class="mb-3">
                <label for="mat_khau" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                    <button type="button" class="btn btn-outline-secondary" id="show-password-btn">Hiển thị</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="loai_nhan_vien" class="form-label">Loại nhân viên</label>
                <select name="loai_nhan_vien" class="form-control" required>
                    <option value="ban_hang">Nhân viên bán hàng</option>
                    <option value="ke_toan">Nhân viên kế toán</option>
                    <option value="kho">Nhân viên kho</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Thêm Nhân viên</button>
        </form>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // JavaScript to toggle password visibility
    document.getElementById('show-password-btn').addEventListener('click', function() {
        var passwordField = document.getElementById('mat_khau');
        var passwordType = passwordField.type;

        // Toggle password visibility
        if (passwordType === 'password') {
            passwordField.type = 'text';
            this.textContent = 'Ẩn mật khẩu'; // Change button text
        } else {
            passwordField.type = 'password';
            this.textContent = 'Hiển thị'; // Change button text back
        }
    });
    </script>
</body>

</html>
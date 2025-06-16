<?php
include '../config/db.php';
include 'header.html';

// Lấy ID nhân viên từ URL
$employee_id = $_GET['id'];

// Truy vấn thông tin nhân viên
$sql = "SELECT * FROM nhan_vien WHERE id = $employee_id";
$employee_result = $conn->query($sql);
$employee = $employee_result->fetch_assoc();

// Lấy danh sách loại nhân viên
$employee_types = ['ban_hang' => 'Nhân viên bán hàng', 'ke_toan' => 'Nhân viên kế toán', 'kho' => 'Nhân viên kho'];

// Cập nhật thông tin nhân viên khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $email_cong_ty = $_POST['email_cong_ty'];
    $sdt = $_POST['sdt'];
    $loai_nhan_vien = $_POST['loai_nhan_vien'];
    
    // Nếu mật khẩu mới được nhập
    $mat_khau = $_POST['mat_khau'] ? sha1($_POST['mat_khau']) : $employee['mat_khau'];

    // Cập nhật nhân viên vào bảng nhan_vien
    $sql_update = "UPDATE nhan_vien SET 
                    ho_ten = '$ho_ten',
                    email_cong_ty = '$email_cong_ty',
                    sdt = '$sdt',
                    mat_khau = '$mat_khau',
                    loai_nhan_vien = '$loai_nhan_vien'
                   WHERE id = $employee_id";
    if ($conn->query($sql_update) === TRUE) {
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
    <title>Sửa Nhân viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <!-- Sửa nhân viên -->
    <div class="container my-5">
        <h2 class="text-center">Sửa Nhân viên</h2>

        <form method="POST" action="edit_employee.php?id=<?= $employee_id ?>">
            <div class="mb-3">
                <label for="ho_ten" class="form-label">Tên nhân viên</label>
                <input type="text" class="form-control" id="ho_ten" name="ho_ten" value="<?= $employee['ho_ten'] ?>"
                    required>
            </div>

            <div class="mb-3">
                <label for="email_cong_ty" class="form-label">Email công ty</label>
                <input type="email" class="form-control" id="email_cong_ty" name="email_cong_ty"
                    value="<?= $employee['email_cong_ty'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="sdt" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="sdt" name="sdt" value="<?= $employee['sdt'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="mat_khau" class="form-label">Sửa mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="mat_khau" name="mat_khau">
                    <button type="button" class="btn btn-outline-secondary" id="show-password-btn">Hiển thị</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="loai_nhan_vien" class="form-label">Loại nhân viên</label>
                <select name="loai_nhan_vien" class="form-control" required>
                    <?php foreach ($employee_types as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $employee['loai_nhan_vien'] == $key ? 'selected' : '' ?>>
                        <?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Cập nhật Nhân viên</button>
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
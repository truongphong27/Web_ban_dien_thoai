<?php
include '../config/db.php';
include 'header.html';
// Lấy danh sách vai trò
$sql_roles = "SELECT * FROM vai_tro";
$roles_result = $conn->query($sql_roles);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ho_ten = $_POST['ho_ten'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $dia_chi = $_POST['dia_chi'];
    $vai_tro_id = $_POST['vai_tro_id'];
    $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT); // Mã hóa mật khẩu

    // Thêm khách hàng vào cơ sở dữ liệu
    $sql_insert = "INSERT INTO khach_hang (ho_ten, sdt, email, dia_chi, vai_tro_id, mat_khau) 
                   VALUES ('$ho_ten', '$sdt', '$email', '$dia_chi', '$vai_tro_id', '$mat_khau')";

    if ($conn->query($sql_insert) === TRUE) {
        header("Location: customer_management.php");
        exit();
    } else {
        echo "Lỗi khi thêm khách hàng: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Thêm Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>


    <!-- Form thêm khách hàng -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Thêm Khách Hàng Mới</h2>
        <form method="POST" action="add_customer.php">
            <div class="mb-3">
                <label for="ho_ten" class="form-label">Họ tên</label>
                <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
            </div>

            <div class="mb-3">
                <label for="sdt" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="sdt" name="sdt" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="dia_chi" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="dia_chi" name="dia_chi" required>
            </div>

            <div class="mb-3">
                <label for="vai_tro_id" class="form-label">Vai trò</label>
                <select name="vai_tro_id" class="form-control" required>
                    <?php while ($role = $roles_result->fetch_assoc()): ?>
                    <?php if ($role['ten_vai_tro'] === 'khach_hang'): ?>
                    <option value="<?= $role['id'] ?>" selected>
                        <?= $role['ten_vai_tro'] ?>
                    </option>
                    <?php endif; ?>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="mat_khau" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePasswordVisibility('mat_khau')">Xem</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="nhap_lai_mat_khau" class="form-label">Nhập lại mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="nhap_lai_mat_khau" name="nhap_lai_mat_khau"
                        required>
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePasswordVisibility('nhap_lai_mat_khau')">Xem</button>
                </div>
            </div>

            <script>
            function togglePasswordVisibility(fieldId) {
                const field = document.getElementById(fieldId);
                field.type = field.type === 'password' ? 'text' : 'password';
            }
            </script>

    </div>

    <div class="text-center">
        <button type="submit" style="height: 50px;" class="btn btn-success w-30 m-4">Thêm khách hàng</button>
    </div>
    </form>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
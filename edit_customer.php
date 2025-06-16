<?php
include '../config/db.php';
include 'header.html';
// Lấy ID khách hàng từ URL
$customer_id = $_GET['id'];

// Truy vấn thông tin khách hàng
$sql = "SELECT * FROM khach_hang WHERE id = $customer_id";
$result = $conn->query($sql);
$customer = $result->fetch_assoc();

// Cập nhật thông tin khách hàng khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $dia_chi = $_POST['dia_chi'];
    $mat_khau = $_POST['mat_khau'];
    
    // Nếu mật khẩu mới được nhập, tiến hành cập nhật mật khẩu
    if (!empty($mat_khau)) {
        $mat_khau = password_hash($mat_khau, PASSWORD_DEFAULT);  // Mã hóa mật khẩu
        $sql_update = "UPDATE khach_hang SET 
                        ho_ten = '$ho_ten',
                        sdt = '$sdt',
                        email = '$email',
                        dia_chi = '$dia_chi',
                        mat_khau = '$mat_khau'
                       WHERE id = $customer_id";
    } else {
        $sql_update = "UPDATE khach_hang SET 
                        ho_ten = '$ho_ten',
                        sdt = '$sdt',
                        email = '$email',
                        dia_chi = '$dia_chi'
                       WHERE id = $customer_id";
    }

    $conn->query($sql_update);

    // Quay lại danh sách khách hàng
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sửa Thông tin Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Sửa Thông tin Khách hàng</h2>

        <form method="POST">
            <div class="mb-3">
                <label for="ho_ten" class="form-label">Tên khách hàng</label>
                <input type="text" class="form-control" id="ho_ten" name="ho_ten" value="<?= $customer['ho_ten'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="sdt" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="sdt" name="sdt" value="<?= $customer['sdt'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $customer['email'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="dia_chi" class="form-label">Địa chỉ</label>
                <textarea class="form-control" id="dia_chi" name="dia_chi"
                    required><?= $customer['dia_chi'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="mat_khau" class="form-label">Mật khẩu mới (Nếu muốn thay đổi)</label>
                <input type="password" class="form-control" id="mat_khau" name="mat_khau"
                    placeholder="Nhập mật khẩu mới (nếu có)">
            </div>

            <button type="submit" class="btn btn-success w-100">Cập nhật khách hàng</button>
        </form>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
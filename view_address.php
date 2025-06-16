<?php
include '../config/db.php';
include 'header.html';
// Lấy ID khách hàng từ URL
$customer_id = $_GET['id'];

// Truy vấn địa chỉ giao hàng của khách hàng
$sql = "SELECT * FROM dia_chi_giao_hang WHERE khach_hang_id = $customer_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Địa chỉ giao hàng của khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>


    <!-- Danh sách địa chỉ giao hàng -->
    <div class="container my-5">
        <h2 class="mb-4 text-center">Địa chỉ giao hàng của khách hàng</h2>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người nhận</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['ten_nguoi_nhan'] ?></td>
                    <td><?= $row['dia_chi'] ?></td>
                    <td><?= $row['so_dien_thoai'] ?></td>
                    <td>
                        <a href="edit_address.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="delete_address.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
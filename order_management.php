<?php
include '../config/db.php';
include 'header.html';
// Truy vấn tất cả đơn hàng
$sql = "SELECT * FROM don_hang ORDER BY id DESC";
$result = $conn->query($sql);

// Lấy trạng thái các đơn hàng
$status_list = [
    'cho_xu_ly' => 'Chờ xử lý',
    'dang_giao' => 'Đang giao',
    'da_giao' => 'Đã giao',
    'huy' => 'Hủy'
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quản lý Đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>


    <!-- Danh sách đơn hàng -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Danh sách Đơn hàng</h2>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hình thức thanh toán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?php
                            $customer_sql = "SELECT ho_ten FROM khach_hang WHERE id = " . $row['khach_hang_id'];
                            $customer_result = $conn->query($customer_sql);
                            $customer = $customer_result->fetch_assoc();
                            echo $customer['ho_ten'];
                        ?>
                    </td>
                    <td><?= $row['ngay_dat'] ?></td>
                    <td><?= number_format($row['tong_tien']) ?>₫</td>
                    <td><?= $status_list[$row['trang_thai']] ?></td>
                    <td><?= $row['hinh_thuc_thanh_toan'] ?></td>
                    <td>
                        <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Chi tiết</a>
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
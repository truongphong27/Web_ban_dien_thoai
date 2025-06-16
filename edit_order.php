<?php
include '../config/db.php';
include 'header.html';

// Lấy ID đơn hàng từ URL
$order_id = $_GET['id'];

// Xử lý cập nhật đơn hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trang_thai = $_POST['trang_thai'];
    $hinh_thuc_thanh_toan = $_POST['hinh_thuc_thanh_toan'];
    $trang_thai_thanh_toan = $_POST['trang_thai_thanh_toan'];

    // Cập nhật thông tin đơn hàng
    $update_order_sql = "UPDATE don_hang SET 
                        trang_thai = '$trang_thai',
                        hinh_thuc_thanh_toan = '$hinh_thuc_thanh_toan',
                        trang_thai_thanh_toan = '$trang_thai_thanh_toan'
                        WHERE id = $order_id";
    
    // Không cập nhật địa chỉ giao hàng
    if ($conn->query($update_order_sql) === TRUE) {
        $success_message = "Cập nhật đơn hàng thành công!";
        // Refresh data sau khi cập nhật
        header("Location: edit_order.php?id=$order_id&success=1");
        exit();
    } else {
        $error_message = "Lỗi: " . $conn->error;
    }
}

// Hiển thị thông báo success từ URL parameter
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Cập nhật đơn hàng thành công!";
}

// Truy vấn thông tin đơn hàng
$sql = "SELECT * FROM don_hang WHERE id = $order_id";
$order_result = $conn->query($sql);
$order = $order_result->fetch_assoc();

// Truy vấn chi tiết sản phẩm trong đơn hàng
$sql_details = "SELECT * FROM chi_tiet_don_hang WHERE don_hang_id = $order_id";
$details_result = $conn->query($sql_details);

// Lấy thông tin khách hàng
$customer_sql = "SELECT * FROM khach_hang WHERE id = " . $order['khach_hang_id'];
$customer_result = $conn->query($customer_sql);
$customer = $customer_result->fetch_assoc();

// Lấy thông tin địa chỉ giao hàng và tên người nhận
$address_sql = "SELECT * FROM dia_chi_giao_hang WHERE khach_hang_id = " . $order['khach_hang_id'];
$address_result = $conn->query($address_sql);
$address = $address_result->fetch_assoc();

// Lấy trạng thái của đơn hàng
$status_list = [
    'cho_xu_ly' => 'Chờ xử lý',
    'dang_giao' => 'Đang giao',
    'da_giao' => 'Đã giao',
    'huy' => 'Hủy'
];

// Lấy các hình thức thanh toán
$payment_methods = ['COD', 'Chuyen_khoan', 'VNPay'];

// Lấy trạng thái thanh toán (chưa thanh toán / đã thanh toán)
$payment_status_list = [
    'chua_thanh_toan' => 'Chưa thanh toán',
    'da_thanh_toan' => 'Đã thanh toán'
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chỉnh sửa Đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>

    <!-- Chỉnh sửa đơn hàng -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Chỉnh sửa Đơn hàng #<?= $order['id'] ?></h2>

        <!-- Hiển thị thông báo -->
        <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Thông tin khách hàng -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h4>Thông tin khách hàng</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <td><?= $customer['id'] ?></td>
                    </tr>
                    <tr>
                        <th>Tên khách hàng</th>
                        <td><?= $customer['ho_ten'] ?></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td><?= $customer['sdt'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $customer['email'] ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Thông tin giao hàng</h4>
                <!-- Không cập nhật địa chỉ giao hàng -->
                <div class="mb-3">
                    <label for="dia_chi" class="form-label">Địa chỉ giao hàng</label>
                    <textarea class="form-control" id="dia_chi" name="dia_chi" rows="3"
                        disabled><?= $address['dia_chi'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="ten_nguoi_nhan" class="form-label">Tên người nhận</label>
                    <input type="text" class="form-control" id="ten_nguoi_nhan" name="ten_nguoi_nhan"
                        value="<?= $address['ten_nguoi_nhan'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="so_dien_thoai_giao_hang" class="form-label">Số điện thoại giao hàng</label>
                    <input type="tel" class="form-control" id="so_dien_thoai_giao_hang" name="so_dien_thoai_giao_hang"
                        value="<?= $address['so_dien_thoai'] ?>" disabled>
                </div>
            </div>
        </div>

        <!-- Form chỉnh sửa thông tin đơn hàng -->
        <form method="POST">
            <h4>Thông tin đơn hàng</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td><?= $order['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Ngày đặt</th>
                            <td><?= $order['ngay_dat'] ?></td>
                        </tr>
                        <tr>
                            <th>Tổng tiền</th>
                            <td><?= number_format($order['tong_tien']) ?>₫</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="trang_thai" class="form-label">Trạng thái đơn hàng</label>
                        <select class="form-select" id="trang_thai" name="trang_thai" required>
                            <?php foreach ($status_list as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($order['trang_thai'] == $key) ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="hinh_thuc_thanh_toan" class="form-label">Hình thức thanh toán</label>
                        <select class="form-select" id="hinh_thuc_thanh_toan" name="hinh_thuc_thanh_toan" required>
                            <?php foreach ($payment_methods as $method): ?>
                            <option value="<?= $method ?>"
                                <?= ($order['hinh_thuc_thanh_toan'] == $method) ? 'selected' : '' ?>>
                                <?= $method ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="trang_thai_thanh_toan" class="form-label">Trạng thái thanh toán</label>
                        <select class="form-select" id="trang_thai_thanh_toan" name="trang_thai_thanh_toan" required>
                            <?php foreach ($payment_status_list as $key => $value): ?>
                            <option value="<?= $key ?>"
                                <?= ($order['trang_thai_thanh_toan'] == $key) ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Chi tiết sản phẩm trong đơn hàng (chỉ xem) -->
            <h4>Chi tiết sản phẩm trong đơn hàng</h4>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Thành tiền</th>
                        <th>Dung lượng</th>
                        <th>Màu sắc</th>
                        <th>Phần trăm giảm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($detail = $details_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php
                                $product_sql = "SELECT ten_san_pham, dung_luong, mau_sac, phan_tram_giam FROM san_pham WHERE id = " . $detail['san_pham_id'];
                                $product_result = $conn->query($product_sql);
                                $product = $product_result->fetch_assoc();
                                echo $product['ten_san_pham'];
                            ?>
                        </td>
                        <td><?= $detail['so_luong'] ?></td>
                        <td><?= number_format($detail['gia']) ?>₫</td>
                        <td><?= number_format($detail['so_luong'] * $detail['gia']) ?>₫</td>
                        <td><?= $product['dung_luong'] ?></td>
                        <td><?= $product['mau_sac'] ?></td>
                        <td><?= $product['phan_tram_giam'] ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Nút thao tác -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-save"></i> Cập nhật đơn hàng
                </button>
                <a href="order_details.php?id=<?= $order_id ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-eye"></i> Xem chi tiết
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

</html>
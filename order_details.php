<?php
include '../config/db.php';
include 'header.html';
// Lấy ID đơn hàng từ URL
$order_id = $_GET['id'];

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
    <title>Chi tiết Đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>


    <!-- Chi tiết đơn hàng -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Chi tiết Đơn hàng</h2>

        <!-- Thông tin khách hàng -->
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
            <tr>
                <th>Địa chỉ giao hàng</th>
                <td><?= $address['dia_chi'] ?></td>
            </tr>
            <tr>
                <th>Số điện thoại giao hàng</th>
                <td><?= $address['so_dien_thoai'] ?></td>
            </tr>
            <tr>
                <th>Tên người nhận</th>
                <td><?= $address['ten_nguoi_nhan'] ?></td>
            </tr>
        </table>

        <!-- Thông tin đơn hàng -->
        <h4>Thông tin đơn hàng</h4>
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
            <tr>
                <th>Trạng thái đơn hàng</th>
                <td><?= $status_list[$order['trang_thai']] ?></td>
            </tr>
            <tr>
                <th>Hình thức thanh toán</th>
                <td><?= $order['hinh_thuc_thanh_toan'] ?></td>
            </tr>
            <tr>
                <th>Trạng thái thanh toán</th>
                <td><?= $payment_status_list[$order['trang_thai_thanh_toan']] ?></td>
            </tr>
        </table>

        <!-- Chi tiết sản phẩm trong đơn hàng -->
        <h4>Chi tiết sản phẩm trong đơn hàng</h4>
        <table class="table table-bordered">
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
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
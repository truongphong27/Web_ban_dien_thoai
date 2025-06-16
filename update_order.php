<?php
include '../config/db.php';

// Lấy dữ liệu từ form
$order_id = $_POST['order_id'];
$status = $_POST['status'];
$payment_method = $_POST['payment_method'];
$total = $_POST['total'];

// Cập nhật đơn hàng
$sql_update = "UPDATE don_hang SET 
                trang_thai = '$status', 
                hinh_thuc_thanh_toan = '$payment_method', 
                tong_tien = $total 
                WHERE id = $order_id";

if ($conn->query($sql_update) === TRUE) {
    // Quay lại trang quản lý đơn hàng sau khi cập nhật thành công
    header("Location: order_management.php");
    exit();
} else {
    echo "Lỗi khi cập nhật đơn hàng: " . $conn->error;
}
?>
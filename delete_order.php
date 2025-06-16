<?php
include '../config/db.php';

// Lấy ID đơn hàng từ URL
$order_id = $_GET['id'];

// Xóa các chi tiết đơn hàng liên quan đến đơn hàng này
$sql_details = "DELETE FROM chi_tiet_don_hang WHERE don_hang_id = $order_id";
$conn->query($sql_details);

// Xóa bảo hành (nếu có) liên quan đến đơn hàng này
$sql_warranty = "DELETE FROM bao_hanh WHERE chi_tiet_don_hang_id IN (SELECT id FROM chi_tiet_don_hang WHERE don_hang_id = $order_id)";
$conn->query($sql_warranty);

// Xóa đơn hàng
$sql_delete = "DELETE FROM don_hang WHERE id = $order_id";
if ($conn->query($sql_delete) === TRUE) {
    // Quay lại trang quản lý đơn hàng sau khi xóa thành công
    header("Location: order_management.php");
    exit();
} else {
    echo "Lỗi khi xóa đơn hàng: " . $conn->error;
}
?>
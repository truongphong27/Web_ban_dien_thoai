<?php
include '../config/db.php';

// Lấy ID khách hàng từ URL
$customer_id = $_GET['id'];

// Xóa khách hàng khỏi cơ sở dữ liệu
$sql_delete = "DELETE FROM khach_hang WHERE id = $customer_id";
if ($conn->query($sql_delete) === TRUE) {
    // Quay lại trang quản lý khách hàng sau khi xóa thành công
    header("Location: customer_management.php");
    exit();
} else {
    echo "Lỗi khi xóa khách hàng: " . $conn->error;
}
?>

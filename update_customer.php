<?php
include '../config/db.php';

// Lấy dữ liệu từ form
$customer_id = $_POST['customer_id'];
$ho_ten = $_POST['ho_ten'];
$sdt = $_POST['sdt'];
$email = $_POST['email'];
$dia_chi = $_POST['dia_chi'];

// Cập nhật thông tin khách hàng
$sql_update = "UPDATE khach_hang SET 
                ho_ten = '$ho_ten',
                sdt = '$sdt',
                email = '$email',
                dia_chi = '$dia_chi'
                WHERE id = $customer_id";

if ($conn->query($sql_update) === TRUE) {
    // Quay lại trang quản lý khách hàng sau khi cập nhật thành công
    header("Location: customer_management.php");
    exit();
} else {
    echo "Lỗi khi cập nhật thông tin khách hàng: " . $conn->error;
}
?>
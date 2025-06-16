<?php
include '../config/db.php';

// Kiểm tra ID mã giảm giá
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Mã giảm giá không tồn tại.");
}

$id = $_GET['id'];

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Xoá mã giảm giá khỏi bảng `ma_giam_gia`
    $stmt = $conn->prepare("DELETE FROM ma_giam_gia WHERE id = ?");
    if (!$stmt) {
        die("Lỗi prepare câu lệnh xoá mã giảm giá: " . $conn->error); // Kiểm tra lỗi
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
    echo "<script>alert('Xoá mã giảm giá thành công!'); window.location.href='index.php';</script>";
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo "Lỗi: " . $e->getMessage();
}
?>
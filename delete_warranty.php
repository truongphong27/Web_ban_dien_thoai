<?php
include '../config/db.php';

// Kiểm tra ID bảo hành
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Bảo hành không tồn tại.");
}

$id = $_GET['id'];

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Xoá bảo hành khỏi bảng `bao_hanh`
    $stmt = $conn->prepare("DELETE FROM bao_hanh WHERE id = ?");
    if (!$stmt) {
        die("Lỗi prepare câu lệnh xoá bảo hành: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
    echo "<script>alert('Xoá bảo hành thành công!'); window.location.href='warranty_list.php';</script>";
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo "Lỗi: " . $e->getMessage();
}
?>
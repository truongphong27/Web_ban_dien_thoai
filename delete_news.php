<?php
include '../config/db.php';

// Kiểm tra nếu có ID bài viết
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Bài viết không tồn tại.");
}

$id = $_GET['id'];

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Xoá các hình ảnh chi tiết bài viết
    $stmt = $conn->prepare("SELECT noidung_hoac_hinh FROM bai_viet_chi_tiet WHERE bai_viet_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Xoá hình ảnh khỏi thư mục images
        $file_path = "../images/" . $row['noidung_hoac_hinh'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Xoá chi tiết bài viết
    $stmt = $conn->prepare("DELETE FROM bai_viet_chi_tiet WHERE bai_viet_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Xoá bài viết chính
    $stmt = $conn->prepare("DELETE FROM bai_viet WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
    echo "<script>alert('Xoá bài viết thành công!'); window.location.href='index.php';</script>";
} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    $conn->rollback();
    echo "Lỗi: " . $e->getMessage();
}
?>
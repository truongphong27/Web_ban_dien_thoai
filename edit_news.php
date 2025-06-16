<?php
include '../config/db.php';
include 'header.html';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra nếu có ID bài viết
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Bài viết không tồn tại.");
}

$id = $_GET['id'];

// Lấy thông tin bài viết từ CSDL
$stmt = $conn->prepare("SELECT * FROM bai_viet WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$bai_viet = $result->fetch_assoc();

// Nếu không tìm thấy bài viết
if (!$bai_viet) {
    die("Không tìm thấy bài viết.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $slug = $_POST['slug'];
    $noi_dung = $_POST['noi_dung'];
    $tac_gia = $_POST['tac_gia'];
    $loai_bai_viet = $_POST['loai_bai_viet'];
    $hien_thi = $_POST['hien_thi'];

    // Upload ảnh đại diện nếu có
    $thumbnail_name = $bai_viet['thumbnail'];  // Giữ ảnh cũ nếu không thay đổi
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_name = uniqid() . '_' . basename($_FILES['thumbnail']['name']);
        move_uploaded_file($thumbnail_tmp, "../img/" . $thumbnail_name);
    }

    $conn->begin_transaction();

    try {
        // Cập nhật bài viết
        $stmt = $conn->prepare("UPDATE bai_viet SET tieu_de = ?, slug = ?, noi_dung = ?, thumbnail = ?, loai_bai_viet = ?, hien_thi = ?, tac_gia_text = ?
            WHERE id = ?");
        $stmt->bind_param("sssssssi", $tieu_de, $slug, $noi_dung, $thumbnail_name, $loai_bai_viet, $hien_thi, $tac_gia, $id);
        $stmt->execute();

        // Cập nhật nội dung chi tiết
        $ds_noi_dung = $_POST['doan_noi_dung'] ?? [];
        $thu_tu_nd = $_POST['thu_tu_noi_dung'] ?? [];

        foreach ($ds_noi_dung as $i => $nd) {
            $thu_tu = $thu_tu_nd[$i] ?? $i;
            $stmt_ct = $conn->prepare("INSERT INTO bai_viet_chi_tiet (bai_viet_id, loai, noidung_hoac_hinh, thu_tu) 
                                       VALUES (?, 'noi_dung', ?, ?)");
            $stmt_ct->bind_param("isi", $id, $nd, $thu_tu);
            $stmt_ct->execute();
        }

        // Hình ảnh chi tiết
        foreach ($_FILES['hinh_anh']['name'] as $i => $filename) {
            if ($_FILES['hinh_anh']['error'][$i] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['hinh_anh']['tmp_name'][$i];
                $new_name = uniqid() . '_' . basename($filename);
                move_uploaded_file($tmp_name, '../img/' . $new_name);

                $thu_tu = $_POST['thu_tu_hinh_anh'][$i];
                $stmt_img = $conn->prepare("INSERT INTO bai_viet_chi_tiet (bai_viet_id, loai, noidung_hoac_hinh, thu_tu)
                                            VALUES (?, 'hinh_anh', ?, ?)");
                $stmt_img->bind_param("isi", $id, $new_name, $thu_tu);
                $stmt_img->execute();
            }
        }

        $conn->commit();
        echo "<script>alert('Cập nhật bài viết thành công!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cập nhật bài viết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function addField(type) {
        const container = document.getElementById(type === 'image' ? 'imageFields' : 'contentFields');
        const div = document.createElement('div');
        div.classList.add('mb-3');

        const inputOrder = document.createElement('input');
        inputOrder.type = 'text';
        inputOrder.name = (type === 'content' ? 'thu_tu_noi_dung[]' : 'thu_tu_hinh_anh[]');
        inputOrder.placeholder = 'Thứ tự hiển thị';
        inputOrder.classList.add('form-control', 'mb-1');

        let input;
        if (type === 'content') {
            input = document.createElement('textarea');
            input.name = 'doan_noi_dung[]';
            input.classList.add('form-control', 'mb-1');
            input.rows = 4;
        } else {
            input = document.createElement('input');
            input.type = 'file';
            input.name = 'hinh_anh[]';
            input.classList.add('form-control', 'mb-1');
        }

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerText = 'Xoá';
        btn.classList.add('btn', 'btn-danger', 'btn-sm');
        btn.onclick = () => div.remove();

        div.appendChild(input);
        div.appendChild(inputOrder);
        div.appendChild(btn);
        container.appendChild(div);
    }
    </script>
</head>

<body>
    <div class="container my-5">
        <h2>Cập nhật bài viết</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="tieu_de" class="form-control" value="<?= $bai_viet['tieu_de'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="<?= $bai_viet['slug'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung tóm tắt</label>
                <textarea name="noi_dung" class="form-control" rows="4"><?= $bai_viet['noi_dung'] ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Ảnh đại diện</label>
                <input type="file" name="thumbnail" class="form-control">
                <small>Hiện tại: <?= $bai_viet['thumbnail'] ?></small>
            </div>
            <div class="mb-3">
                <label class="form-label">Tác giả</label>
                <input type="text" name="tac_gia" class="form-control" value="<?= $bai_viet['tac_gia_text'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label class="form-label">Loại bài viết</label>
                <select name="loai_bai_viet" class="form-control" required>
                    <option value="tin_tuc" <?= $bai_viet['loai_bai_viet'] == 'tin_tuc' ? 'selected' : '' ?>>Tin tức
                    </option>
                    <option value="video" <?= $bai_viet['loai_bai_viet'] == 'video' ? 'selected' : '' ?>>Video</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Hiển thị</label>
                <select name="hien_thi" class="form-control">
                    <option value="1" <?= $bai_viet['hien_thi'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= $bai_viet['hien_thi'] == 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nội dung chi tiết</label>
                <div id="contentFields"></div>
                <button type="button" class="btn btn-secondary mt-2" onclick="addField('content')">+ Nội dung</button>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình ảnh bài viết</label>
                <div id="imageFields"></div>
                <button type="button" class="btn btn-secondary mt-2" onclick="addField('image')">+ Hình ảnh</button>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật bài viết</button>
        </form>
    </div>
</body>

</html>
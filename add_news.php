<?php
include '../config/db.php';
include 'header.html';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $slug = $_POST['slug'];
    $noi_dung = $_POST['noi_dung'];
    $tac_gia = $_POST['tac_gia'];
    $loai_bai_viet = $_POST['loai_bai_viet'];
    $hien_thi = $_POST['hien_thi'];

    // Upload ảnh đại diện
    $thumbnail_name = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_name = uniqid() . '_' . basename($_FILES['thumbnail']['name']);
        move_uploaded_file($thumbnail_tmp, "../img/" . $thumbnail_name);
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO bai_viet (tieu_de, slug, noi_dung, thumbnail, loai_bai_viet, hien_thi, ngay_dang, tac_gia_text, tac_gia_admin_id) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 1)");
        $stmt->bind_param("sssssis", $tieu_de, $slug, $noi_dung, $thumbnail_name, $loai_bai_viet, $hien_thi, $tac_gia);
        $stmt->execute();
        $bai_viet_id = $conn->insert_id;

        // Nội dung chi tiết
        $ds_noi_dung = $_POST['doan_noi_dung'] ?? [];
        $thu_tu_nd = $_POST['thu_tu_noi_dung'] ?? [];

        foreach ($ds_noi_dung as $i => $nd) {
            $thu_tu = $thu_tu_nd[$i] ?? $i;
            $stmt = $conn->prepare("INSERT INTO bai_viet_chi_tiet (bai_viet_id, loai, noidung_hoac_hinh, thu_tu) 
                VALUES (?, 'noi_dung', ?, ?)");
            $stmt->bind_param("isi", $bai_viet_id, $nd, $thu_tu);
            $stmt->execute();
        }

        // Hình ảnh
        foreach ($_FILES['hinh_anh']['error'] as $i => $err) {
            if ($err === UPLOAD_ERR_OK) {
                $tmp = $_FILES['hinh_anh']['tmp_name'][$i];
                $name = uniqid() . '_' . basename($_FILES['hinh_anh']['name'][$i]);
                move_uploaded_file($tmp, "../img/" . $name);

                $thu_tu = $_POST['thu_tu_hinh_anh'][$i] ?? $i;
                $stmt = $conn->prepare("INSERT INTO bai_viet_chi_tiet (bai_viet_id, loai, noidung_hoac_hinh, thu_tu) 
                    VALUES (?, 'hinh_anh', ?, ?)");
                $stmt->bind_param("isi", $bai_viet_id, $name, $thu_tu);
                $stmt->execute();
            }
        }

        $conn->commit();
        echo "<script>alert('Thêm bài viết thành công'); window.location.href='index.php';</script>";
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
    <title>Thêm Tin Tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function addField(type) {
        let container = document.getElementById(type === 'image' ? 'imageFields' : 'contentFields');
        let div = document.createElement('div');
        div.classList.add('mb-3');

        if (type === 'image') {
            div.innerHTML = `
                    <input type="file" name="hinh_anh[]" class="form-control mb-2" required>
                    <input type="text" name="thu_tu_hinh_anh[]" class="form-control mb-2" placeholder="Thứ tự hiển thị">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">Xoá</button>
                `;
        } else {
            div.innerHTML = `
                    <textarea name="doan_noi_dung[]" rows="4" class="form-control mb-2" placeholder="Nội dung chi tiết" required></textarea>
                    <input type="text" name="thu_tu_noi_dung[]" class="form-control mb-2" placeholder="Thứ tự hiển thị">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">Xoá</button>
                `;
        }

        container.appendChild(div);
    }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h2>Thêm Tin Tức</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Tiêu đề</label>
                <input type="text" name="tieu_de" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nội dung ngắn</label>
                <textarea name="noi_dung" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label>Ảnh đại diện</label>
                <input type="file" name="thumbnail" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Tác giả (tên hiển thị)</label>
                <input type="text" name="tac_gia" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Loại bài viết</label>
                <select name="loai_bai_viet" class="form-control">
                    <option value="tin_tuc">Tin tức</option>
                    <option value="thong_bao">Thông báo</option>
                    <option value="khuyen_mai">Khuyến mãi</option>
                    <option value="dich_vu">Dịch vụ</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Hiển thị</label>
                <select name="hien_thi" class="form-control">
                    <option value="1">Có</option>
                    <option value="0">Không</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Nội dung chi tiết</label>
                <div id="contentFields"></div>
                <button type="button" class="btn btn-secondary mt-2" onclick="addField('content')">+ Nội dung</button>
            </div>

            <div class="mb-3">
                <label>Hình ảnh bài viết</label>
                <div id="imageFields"></div>
                <button type="button" class="btn btn-secondary mt-2" onclick="addField('image')">+ Hình ảnh</button>
            </div>

            <button type="submit" class="btn btn-primary">Thêm bài viết</button>
        </form>
    </div>
</body>

</html>
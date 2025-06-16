<?php
include '../config/db.php';
include '../header.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $slug = $_POST['slug'];
    $noi_dung = $_POST['noi_dung'];
    $thumbnail = $_POST['thumbnail'];
    $loai_bai_viet = $_POST['loai_bai_viet'];
    $hien_thi = $_POST['hien_thi'];
    $tac_gia_admin_id = $_POST['tac_gia_admin_id'];

    $hinh_anh = $_FILES['hinh_anh'];
    $thu_tu_hinh_anh = $_POST['thu_tu_hinh_anh'];
    $doan_noi_dung = $_POST['doan_noi_dung'];
    $thu_tu_noi_dung = $_POST['thu_tu_noi_dung'];

    $conn->begin_transaction();

    try {
        $sql_bai_viet = "INSERT INTO bai_viet (tieu_de, slug, noi_dung, thumbnail, loai_bai_viet, hien_thi, ngay_dang, tac_gia_admin_id)
                         VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $conn->prepare($sql_bai_viet);
        $stmt->bind_param("ssssssi", $tieu_de, $slug, $noi_dung, $thumbnail, $loai_bai_viet, $hien_thi, $tac_gia_admin_id);
        $stmt->execute();
        $bai_viet_id = $stmt->insert_id;

        foreach ($doan_noi_dung as $index => $noidung) {
            $thu_tu = isset($thu_tu_noi_dung[$index]) ? $thu_tu_noi_dung[$index] : $index;
            $sql_nd = "INSERT INTO bai_viet_chi_tiet (bai_viet_id, loai, noidung_hoac_hinh, thu_tu)
                       VALUES (?, 'noi_dung', ?, ?)";
            $stmt = $conn->prepare($sql_nd);
            $stmt->bind_param("isi", $bai_viet_id, $noidung, $thu_tu);
            $stmt->execute();
        }

        $target_dir = "../images/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        foreach ($hinh_anh['name'] as $index => $name) {
            if ($hinh_anh['error'][$index] === UPLOAD_ERR_OK) {
                $tmp_name = $hinh_anh["tmp_name"][$index];
                $image_name = basename($name);
                $target_file = $target_dir . $image_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $thu_tu = isset($thu_tu_hinh_anh[$index]) ? $thu_tu_hinh_anh[$index] : $index;
                    $sql_anh = "INSERT INTO bai_viet_chi_tiet (bai_viet_id, loai, noidung_hoac_hinh, thu_tu)
                                VALUES (?, 'hinh_anh', ?, ?)";
                    $stmt = $conn->prepare($sql_anh);
                    $stmt->bind_param("isi", $bai_viet_id, $image_name, $thu_tu);
                    $stmt->execute();
                } else {
                    throw new Exception("Không thể upload ảnh $image_name.");
                }
            }
        }

        $conn->commit();
        echo "<script>alert('Thêm tin tức thành công!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi: " . $e->getMessage();
    }
}

// Load tác giả (admin)
$admin_sql = "SELECT * FROM quan_tri_vien";
$admin_result = $conn->query($admin_sql);
$loai_bai_viet_list = ['tin_tuc' => 'Tin tức', 'video' => 'Video'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Tin Tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function addField(fieldType) {
        let container = document.getElementById(fieldType + 'Fields');
        let field;
        let orderInput = document.createElement('input');
        orderInput.className = 'form-control mb-2';
        orderInput.setAttribute('name', fieldType === 'image' ? 'thu_tu_hinh_anh[]' : 'thu_tu_noi_dung[]');
        orderInput.setAttribute('placeholder', 'Thứ tự hiển thị');

        if (fieldType === 'image') {
            field = document.createElement('input');
            field.setAttribute('type', 'file');
            field.setAttribute('name', 'hinh_anh[]');
        } else {
            field = document.createElement('textarea');
            field.setAttribute('name', 'doan_noi_dung[]');
            field.rows = 4;
        }
        field.className = 'form-control mb-2';

        let removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger mb-3';
        removeBtn.innerText = 'Xoá';
        removeBtn.onclick = function() {
            container.removeChild(wrapper);
        };

        let wrapper = document.createElement('div');
        wrapper.appendChild(field);
        wrapper.appendChild(orderInput);
        wrapper.appendChild(removeBtn);

        container.appendChild(wrapper);
    }
    </script>
</head>

<body>
    <div class="container mt-4">
        <h3>Thêm bài viết mới</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label>Tiêu đề</label><input type="text" name="tieu_de" class="form-control" required>
            </div>
            <div class="mb-3"><label>Slug</label><input type="text" name="slug" class="form-control" required></div>
            <div class="mb-3"><label>Nội dung mô tả</label><textarea name="noi_dung" class="form-control"
                    required></textarea></div>
            <div class="mb-3"><label>Ảnh đại diện</label><input type="text" name="thumbnail" class="form-control"
                    required></div>
            <div class="mb-3"><label>Loại bài viết</label>
                <select name="loai_bai_viet" class="form-control" required>
                    <?php foreach ($loai_bai_viet_list as $key => $value): ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3"><label>Trạng thái hiển thị</label>
                <select name="hien_thi" class="form-control" required>
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
            </div>
            <div class="mb-3"><label>Tác giả</label>
                <select name="tac_gia_admin_id" class="form-control" required>
                    <?php while ($admin = $admin_result->fetch_assoc()): ?>
                    <option value="<?= $admin['id'] ?>"><?= $admin['ten_dang_nhap'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3" id="contentFields">
                <label>Nội dung chi tiết</label>
                <textarea name="doan_noi_dung[]" class="form-control mb-2" rows="4" required></textarea>
                <input type="text" name="thu_tu_noi_dung[]" class="form-control mb-3" placeholder="Thứ tự hiển thị">
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="addField('content')">Thêm Nội Dung</button>

            <div class="mb-3" id="imageFields">
                <label>Hình ảnh chi tiết</label>
                <input type="file" name="hinh_anh[]" class="form-control mb-2">
                <input type="text" name="thu_tu_hinh_anh[]" class="form-control mb-3" placeholder="Thứ tự hiển thị">
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="addField('image')">Thêm Hình Ảnh</button>

            <button type="submit" class="btn btn-primary">Thêm bài viết</button>
        </form>
    </div>
</body>

</html>
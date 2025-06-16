<?php
include '../config/db.php';
include 'header.html';
// Các biến lọc
$search_query = '';
$status_filter = '';
$order_by = 'id'; // Mặc định sắp xếp theo ID
$order_direction = 'ASC'; // Mặc định theo thứ tự tăng dần

// Số tin tức mỗi trang (có thể tùy chỉnh)
$news_per_page = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Mặc định là 10 tin tức mỗi trang

// Trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $news_per_page;

// Kiểm tra nếu có từ khóa tìm kiếm
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Kiểm tra nếu có lọc theo trạng thái
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

// Kiểm tra nếu có yêu cầu sắp xếp
if (isset($_GET['order_by'])) {
    $order_by = $_GET['order_by'];
}

if (isset($_GET['order_direction'])) {
    $order_direction = $_GET['order_direction'];
}

// SQL cơ bản để lấy dữ liệu bài viết
$sql = "SELECT * FROM bai_viet WHERE 1";

// Thêm điều kiện tìm kiếm
if ($search_query != '') {
    $sql .= " AND (tieu_de LIKE '%$search_query%' OR slug LIKE '%$search_query%')";
}

// Thêm điều kiện lọc theo trạng thái bài viết
if ($status_filter != '') {
    $sql .= " AND hien_thi = '$status_filter'";
}

// Thêm phần sắp xếp bài viết
$sql .= " ORDER BY $order_by $order_direction";

// Thêm điều kiện phân trang
$sql .= " LIMIT $offset, $news_per_page";

// Thực thi truy vấn
$result = $conn->query($sql);

// Lấy tổng số bài viết để tính toán số trang
$total_sql = "SELECT COUNT(*) AS total FROM bai_viet WHERE 1";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_news = $total_row['total'];
$total_pages = ceil($total_news / $news_per_page);

// Trạng thái bài viết
$status_list = [
    1 => 'Hiển thị',
    0 => 'Ẩn'
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quản lý Tin Tức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    /* Tùy chỉnh style cho bộ lọc */
    .filter-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .filter-form input,
    .filter-form select,
    .filter-form button {
        margin-bottom: 10px;
    }

    .filter-form .form-control {
        border-radius: 5px;
    }

    /* Tùy chỉnh nút tìm kiếm và xóa bộ lọc */
    .filter-form button {
        border-radius: 5px;
    }

    /* Thiết kế các phần tử trong bảng bài viết */
    .table th,
    .table td {
        text-align: center;
    }

    /* Đảm bảo form lọc responsive */
    @media (max-width: 768px) {
        .filter-form {
            padding: 10px;
        }

        .filter-form .col-md-2,
        .filter-form .col-md-3 {
            margin-bottom: 10px;
        }

        .filter-form input,
        .filter-form select {
            width: 100%;
        }

        .filter-form button {
            width: 100%;
        }
    }
    </style>
</head>

<body>


    <!-- Tìm kiếm và lọc tin tức -->
    <div class="container my-5">
        <h2 class="mb-4 text-center">Danh sách Tin Tức</h2>

        <!-- Form tìm kiếm và lọc -->
        <form method="GET" class="filter-form row mb-4">
            <!-- Tìm kiếm tin tức -->
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm tin tức"
                    value="<?= htmlspecialchars($search_query) ?>">
            </div>

            <!-- Lọc theo trạng thái -->
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">Trạng thái</option>
                    <?php foreach ($status_list as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $status_filter == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger w-100" name="clear" value="1">Xóa lọc</button>
            </div>
        </form>

        <!-- Sắp xếp tin tức -->
        <form method="GET" class="row mb-4">
            <div class="col-md-2">
                <select name="order_by" class="form-control">
                    <option value="id" <?= $order_by == 'id' ? 'selected' : '' ?>>ID</option>
                    <option value="ngay_dang" <?= $order_by == 'ngay_dang' ? 'selected' : '' ?>>Ngày đăng</option>
                    <option value="tieu_de" <?= $order_by == 'tieu_de' ? 'selected' : '' ?>>Tiêu đề</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="order_direction" class="form-control">
                    <option value="ASC" <?= $order_direction == 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                    <option value="DESC" <?= $order_direction == 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-info w-100">Sắp xếp</button>
            </div>
            <div class="col-md-2">
                <a href="add_news.php" class="btn btn-success w-100">Thêm bài viết</a>
            </div>
        </form>

        <!-- Danh sách tin tức -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th>Loại bài viết</th>
                    <th>Trạng thái</th>
                    <th>Ngày đăng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['tieu_de'] ?></td>
                    <td><?= $row['slug'] ?></td>
                    <td><?= ucfirst($row['loai_bai_viet']) ?></td>
                    <td><?= $status_list[$row['hien_thi']] ?></td>
                    <td><?= $row['ngay_dang'] ?></td>
                    <td>
                        <a href="edit_news.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="delete_news.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <form method="GET" class="row mb-4">
            <div class="col-md-2">
                <select name="limit" class="form-control">
                    <option value="10" <?= $news_per_page == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $news_per_page == 20 ? 'selected' : '' ?>>20</option>
                    <option value="30" <?= $news_per_page == 30 ? 'selected' : '' ?>>30</option>
                    <option value="50" <?= $news_per_page == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $news_per_page == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Hiển thị</button>
            </div>
            <!-- Phân trang -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <?php
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&limit=' . $news_per_page . '">' . $i . '</a></li>';
                    }
                ?>
                </ul>
            </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
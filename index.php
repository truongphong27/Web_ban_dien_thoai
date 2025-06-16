<?php
include '../config/db.php';
include 'header.html';
// Kiểm tra nếu nút "Xóa lọc" được nhấn
if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    // Xóa tất cả các bộ lọc và tìm kiếm
    $search_query = '';
    $category_filter = '';
    $id_filter = '';
    $quantity_filter = '';
    $price_min_filter = '';
    $price_max_filter = '';
    $color_filter = '';  // Lọc theo màu sắc
    $storage_filter = '';  // Lọc theo dung lượng
    $discount_filter = '';  // Lọc theo phần trăm giảm
    $order_by = 'id';  // Sắp xếp theo ID mặc định
    $order_direction = 'ASC';  // Mặc định theo thứ tự tăng dần
}

// Lọc sản phẩm theo các tiêu chí: tìm kiếm, loại sản phẩm, ID, số lượng, giá
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$id_filter = isset($_GET['id']) ? $_GET['id'] : '';
$quantity_filter = isset($_GET['quantity']) ? $_GET['quantity'] : '';
$price_min_filter = isset($_GET['price_min']) ? $_GET['price_min'] : '';
$price_max_filter = isset($_GET['price_max']) ? $_GET['price_max'] : '';
$color_filter = isset($_GET['color']) ? $_GET['color'] : '';  // Lọc theo màu sắc
$storage_filter = isset($_GET['storage']) ? $_GET['storage'] : '';  // Lọc theo dung lượng
$discount_filter = isset($_GET['discount']) ? $_GET['discount'] : '';  // Lọc theo phần trăm giảm
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'id';  // Sắp xếp theo ID mặc định
$order_direction = isset($_GET['order_direction']) ? $_GET['order_direction'] : 'ASC';  // Mặc định theo thứ tự tăng dần

// Số sản phẩm mỗi trang (có thể tùy chỉnh)
$products_per_page = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;  // Mặc định là 10 sản phẩm mỗi trang

// Trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// SQL cơ bản
$sql = "SELECT * FROM san_pham WHERE 1";

// Thêm điều kiện tìm kiếm
if ($search_query != '') {
    $sql .= " AND (ten_san_pham LIKE '%$search_query%' OR mo_ta LIKE '%$search_query%')";
}

// Thêm điều kiện lọc theo loại sản phẩm
if ($category_filter != '') {
    $sql .= " AND loai_id = $category_filter";
}

// Thêm điều kiện lọc theo ID sản phẩm
if ($id_filter != '') {
    $sql .= " AND id = $id_filter";
}

// Thêm điều kiện lọc theo số lượng
if ($quantity_filter != '') {
    $sql .= " AND so_luong >= $quantity_filter";
}

// Thêm điều kiện lọc theo giá
if ($price_min_filter != '') {
    $sql .= " AND gia >= $price_min_filter";
}
if ($price_max_filter != '') {
    $sql .= " AND gia <= $price_max_filter";
}

// Thêm điều kiện lọc theo màu sắc
if ($color_filter != '') {
    $sql .= " AND mau_sac = '$color_filter'";
}

// Thêm điều kiện lọc theo dung lượng
if ($storage_filter != '') {
    $sql .= " AND dung_luong = '$storage_filter'";
}

// Thêm điều kiện lọc theo phần trăm giảm
if ($discount_filter != '') {
    $sql .= " AND phan_tram_giam = $discount_filter";
}

// Thêm phần sắp xếp sản phẩm
$sql .= " ORDER BY $order_by $order_direction";

// Thêm điều kiện phân trang
$sql .= " LIMIT $offset, $products_per_page";

// Thực thi truy vấn
$result = $conn->query($sql);

// Lấy tổng số sản phẩm để tính toán số trang
$total_sql = "SELECT COUNT(*) AS total FROM san_pham WHERE 1";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $products_per_page);

// Lấy tất cả loại sản phẩm để lọc
$sql_categories = "SELECT * FROM loai_san_pham";
$categories_result = $conn->query($sql_categories);

// Lấy tất cả màu sắc để lọc
$sql_colors = "SELECT DISTINCT mau_sac FROM san_pham";
$colors_result = $conn->query($sql_colors);

// Lấy tất cả dung lượng để lọc
$sql_storage = "SELECT DISTINCT dung_luong FROM san_pham";
$storage_result = $conn->query($sql_storage);

// Lấy tất cả phần trăm giảm để lọc
$sql_discounts = "SELECT DISTINCT phan_tram_giam FROM san_pham";
$discounts_result = $conn->query($sql_discounts);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quản lý sản phẩm</title>
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

    /* Thiết kế các phần tử trong bảng sản phẩm */
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


    <!-- Tìm kiếm và lọc sản phẩm -->
    <div class="container my-5">
        <h2 class="mb-4 text-center">Danh sách sản phẩm</h2>

        <!-- Form tìm kiếm và lọc -->
        <form method="GET" class="filter-form row mb-4">
            <!-- Tìm kiếm sản phẩm -->
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm sản phẩm"
                    value="<?= htmlspecialchars($search_query) ?>">
            </div>

            <!-- Lọc theo loại sản phẩm -->
            <div class="col-md-2">
                <select name="category" class="form-control">
                    <option value="">Tất cả loại</option>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $category_filter ? 'selected' : '' ?>>
                        <?= $category['ten_loai'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Lọc theo màu sắc -->
            <div class="col-md-2">
                <select name="color" class="form-control">
                    <option value="">Tất cả màu</option>
                    <?php while ($color = $colors_result->fetch_assoc()): ?>
                    <option value="<?= $color['mau_sac'] ?>"
                        <?= $color['mau_sac'] == $color_filter ? 'selected' : '' ?>>
                        <?= $color['mau_sac'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Lọc theo dung lượng -->
            <div class="col-md-2">
                <select name="storage" class="form-control">
                    <option value="">Tất cả dung lượng</option>
                    <?php while ($storage = $storage_result->fetch_assoc()): ?>
                    <option value="<?= $storage['dung_luong'] ?>"
                        <?= $storage['dung_luong'] == $storage_filter ? 'selected' : '' ?>>
                        <?= $storage['dung_luong'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Lọc theo phần trăm giảm -->
            <div class="col-md-2">
                <select name="discount" class="form-control">
                    <option value="">Tất cả phần trăm giảm</option>
                    <?php while ($discount = $discounts_result->fetch_assoc()): ?>
                    <option value="<?= $discount['phan_tram_giam'] ?>"
                        <?= $discount['phan_tram_giam'] == $discount_filter ? 'selected' : '' ?>>
                        <?= $discount['phan_tram_giam'] ?>%
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Lọc theo ID sản phẩm -->
            <div class="col-md-2">
                <input type="number" class="form-control" name="id" placeholder="ID sản phẩm" value="<?= $id_filter ?>">
            </div>

            <!-- Lọc theo số lượng -->
            <div class="col-md-2">
                <input type="number" class="form-control" name="quantity" placeholder="Số lượng"
                    value="<?= $quantity_filter ?>">
            </div>

            <!-- Lọc theo giá -->
            <div class="col-md-2">
                <input type="number" class="form-control" name="price_min" placeholder="Giá min"
                    value="<?= $price_min_filter ?>">
            </div>

            <div class="col-md-2">
                <input type="number" class="form-control" name="price_max" placeholder="Giá max"
                    value="<?= $price_max_filter ?>">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
            </div>
            <div class="col-md-2">
                <a href="index.php" class="btn btn-danger w-100">Xóa lọc</a>
            </div>
        </form>

        <!-- Sắp xếp sản phẩm -->
        <form method="GET" class="row mb-4">
            <div class="col-md-2">
                <select name="order_by" class="form-control">
                    <option value="id" <?= $order_by == 'id' ? 'selected' : '' ?>>ID</option>
                    <option value="gia" <?= $order_by == 'gia' ? 'selected' : '' ?>>Giá</option>
                    <option value="so_luong" <?= $order_by == 'so_luong' ? 'selected' : '' ?>>Số lượng</option>
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
                <a href="add.php" class="btn btn-success w-100">+ Thêm sản phẩm</a>
            </div>
        </form>

        <!-- Danh sách sản phẩm -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Loại</th>
                    <th>Mô tả</th>
                    <th>Màu sắc</th>
                    <th>Dung lượng</th>
                    <th>Phần trăm giảm</th>
                    <th>Hình ảnh</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['ten_san_pham'] ?></td>
                    <td><?= number_format($row['gia']) ?>₫</td>
                    <td><?= $row['so_luong'] ?></td>
                    <td><?= $row['loai_id'] ?></td>
                    <td><?= $row['mo_ta'] ?></td>
                    <td><?= $row['mau_sac'] ?></td>
                    <td><?= $row['dung_luong'] ?></td>
                    <td><?= $row['phan_tram_giam'] ?>%</td>
                    <td>
                        <?php
                            // Hiển thị tất cả hình ảnh của sản phẩm
                            $sql_image = "SELECT * FROM hinh_anh_san_pham WHERE san_pham_id = " . $row['id'];
                            $result_image = $conn->query($sql_image);
                            while ($image = $result_image->fetch_assoc()) {
                                echo '<img src="' . $image['ten_file'] . '" width="50" height="50" class="m-1">';
                            }
                        ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Chọn số lượng sản phẩm hiển thị mỗi trang -->
        <form method="GET" class="row mb-4">
            <div class="col-md-2">
                <select name="limit" class="form-control">
                    <option value="10" <?= $products_per_page == 10 ? 'selected' : '' ?>>10 sản phẩm</option>
                    <option value="20" <?= $products_per_page == 20 ? 'selected' : '' ?>>20 sản phẩm</option>
                    <option value="30" <?= $products_per_page == 30 ? 'selected' : '' ?>>30 sản phẩm</option>
                    <option value="50" <?= $products_per_page == 50 ? 'selected' : '' ?>>50 sản phẩm</option>
                    <option value="100" <?= $products_per_page == 100 ? 'selected' : '' ?>>100 sản phẩm</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Hiển thị</button>
            </div>
        </form>
        <!-- Phân trang -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&limit=' . $products_per_page . '">' . $i . '</a></li>';
                    }
                ?>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
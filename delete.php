<?php
include_once '../config/db.php';

$id = $_GET['id'];

$sql_delete = "DELETE FROM san_pham WHERE id = $id";
if ($conn->query($sql_delete) === TRUE) {
    header('Location: index.php');
} else {
    echo "Lỗi: " . $conn->error;
}
?>
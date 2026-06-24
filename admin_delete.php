<?php
include("db.php");

$redirect = "admin_index.php";

// 透過 URL 傳入 type (user 或 store) 與 id
if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];

    if ($type === 'user') {
        mysqli_query($conn, "DELETE FROM users WHERE user_id = $id");
        $redirect = "admin_index.php"; // 你原來的列表頁
    } elseif ($type === 'store') {
        mysqli_query($conn, "DELETE FROM stores WHERE store_id = $id");
        $redirect = "admin_stores.php"; // 你剛剛建立的商家管理頁
    }
    elseif ($type === 'review') {
        mysqli_query($conn, "DELETE FROM store_reviews WHERE No = $id");
        $redirect = "admin_reviews.php"; // 刪除後跳回評論管理頁
    }
}

// 刪除後跳轉回對應頁面
header("Location: $redirect");
exit();
?>
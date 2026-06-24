<?php
session_start();
include("db.php");

$redirect = "admin_index.php";

// 1. 取得類型與 ID (例如: admin_edit.php?type=user&id=1)
$type = $_GET['type'] ?? 'user'; 
$id = intval($_GET['id'] ?? 0);

if (!$id) { die("缺少 ID"); }

// 2. 設定表與欄位變數
if ($type === 'store') {
    $table = "stores";
    $id_col = "store_id";
    $name_col = "store_name";
} else {
    $table = "users";
    $id_col = "user_id";
    $name_col = "user_name";
}

// 3. 更新邏輯
// 3. 更新邏輯
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    if ($type === 'user') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $allergens = mysqli_real_escape_string($conn, $_POST['allergens']);
        $sql = "UPDATE users SET user_name='$name', email='$email', phone='$phone', allergens='$allergens' WHERE user_id=$id";
    } else {
        // 商家欄位處理
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $sql = "UPDATE stores SET store_name='$name', phone='$phone', email='$email', address='$address', description='$description' WHERE store_id=$id";
    }
    
    if (mysqli_query($conn, $sql)) {
        $redirect = ($type === 'store') ? 'admin_stores.php' : 'admin_index.php';
        echo "<script>alert('更新成功！'); window.location.href='/ECO剩食/$redirect';</script>";
    } else {
        echo "更新失敗: " . mysqli_error($conn);
    }
}

// 4. 撈取資料
$result = mysqli_query($conn, "SELECT * FROM $table WHERE $id_col = $id");
$data = mysqli_fetch_assoc($result);

include("admin-header.php");
?>

<main style="max-width: 600px; margin: 100px auto; padding: 20px;">
    <div class="user-card" style="display:block;">
        <h2>編輯 <?php echo ($type === 'store' ? '商家' : '使用者'); ?> 資訊</h2>
<form method="POST">
    <div style="margin-bottom: 15px;">
        <label>名稱</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($data[$name_col] ?? ''); ?>" required style="width:100%; padding: 8px;">
    </div>
    
    <div style="margin-bottom: 15px;"><label>郵件</label><input type="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" style="width:100%; padding: 8px;"></div>
    <div style="margin-bottom: 15px;"><label>電話</label><input type="text" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" style="width:100%; padding: 8px;"></div>
    
    <?php if ($type === 'user'): ?>
        <div style="margin-bottom: 15px;"><label>過敏原</label><input type="text" name="allergens" value="<?php echo htmlspecialchars($data['allergens'] ?? ''); ?>" style="width:100%; padding: 8px;"></div>
    <?php else: ?>
        <div style="margin-bottom: 15px;"><label>地址</label><input type="text" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>" style="width:100%; padding: 8px;"></div>
        <div style="margin-bottom: 15px;"><label>介紹</label><textarea name="description" style="width:100%; padding: 8px;"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea></div>
    <?php endif; ?>

    <button type="submit" class="btn btn-edit" style="width:100%; padding: 15px;">確認修改</button>
</form>
    </div>
</main>
<?php
// db.php (雲端主機專用版)

//$host = "sql304.infinityfree.com"; // 👈 請換成你剛剛在 phpMyAdmin 畫面上看到的伺服器網址
//$user = "if0_42088424";             // 👈 你的 InfinityFree 使用者帳號
//$pass = "ecobox123456789";          // 👈 填入你在 Additional Information 那步自己設定的密碼
//$dbname = "if0_42088424_food_waste";   // 👈 請換成你左側欄位看到的完整資料庫名稱

$conn = mysqli_connect("localhost", "root", "", "food_waste");

if (!$conn) {
    die("雲端資料庫連線失敗: " . mysqli_connect_error());
}

// 設定編碼防止中文亂碼
mysqli_query($conn, "SET NAMES 'utf8mb4'");
?>

 
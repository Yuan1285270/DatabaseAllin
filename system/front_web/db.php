<?php
// db.php - 資料庫連線設定

$servername = "localhost";
$username = "D1285270";
$password = "#w4n7Hhgv";         // XAMPP 預設沒密碼
$database = "D1285270";    // 資料庫名稱（你可以改）

$conn = new mysqli($servername, $username, $password, $database);

// 連線錯誤處理
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}
$db = $conn; // ← 這一行會讓你的 index.php 能使用 $db
?>

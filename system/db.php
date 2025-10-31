<?php
$host = "localhost";  // 或是 "140.134.53.57"
$user = "D1285270";       // 改成你的帳號
$pass = "#w4n7Hhgv";           // 改成你的密碼
$dbname = "D1285270"; // 資料庫名稱

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("連線失敗：" . $conn->connect_error);
}
?>


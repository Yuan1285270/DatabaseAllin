<?php
session_start();
require_once 'db.php'; // 提供 $db = new mysqli(...)

if (!isset($_GET['email'])) {
    die("錯誤：未提供 email。");
}

$email = $_GET['email'];

// 使用 mysqli 準備語句查詢會員
$stmt = $db->prepare("SELECT name FROM members WHERE email = ?");
if (!$stmt) {
    die("預處理失敗：" . $db->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // ✅ 登入成功，儲存 session
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $user['name'];
    header("Location: index.php");
    exit;
} else {
    // ⛔ 未註冊，導向補資料頁
    $_SESSION['google_email'] = $email;
    header("Location: google_register.php");
    exit;
}
?>

<?php
session_start();
require_once 'db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 檢查是否收到 POST 資料
if (!isset($_POST['email']) || !isset($_POST['name']) || !isset($_POST['phone'])) {
    die("❌ 資料不完整，請重新填寫");
}

$email = trim($_POST['email']);
$name = trim($_POST['name']);
$phone = trim($_POST['phone']);

// ✅ 查詢是否已註冊（多重驗證保險）
$stmt = $db->prepare("SELECT member_id FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("❌ 此 Email 已經註冊，請直接登入。");
}
$stmt->close();

// ✅ 自動產生 member_id（3位數）
$result = $db->query("SELECT MAX(CAST(member_id AS UNSIGNED)) AS last_id FROM members");
$row = $result->fetch_assoc();
$last_id = $row['last_id'];

if ($last_id) {
    $next_num = intval($last_id) + 1;
    $member_id = str_pad($next_num, 3, "0", STR_PAD_LEFT);
} else {
    $member_id = "001";
}

// ✅ 設定預設密碼（可改成亂數或提示改密碼）
$defaultPassword = "google123"; // 或產生亂數密碼
$hashed = password_hash($defaultPassword, PASSWORD_DEFAULT);

// ✅ 寫入資料表（level=1, admin_id='001'）
$stmt = $db->prepare("INSERT INTO members (member_id, name, email, password, phone, registration_date, level, admin_id) VALUES (?, ?, ?, ?, ?, NOW(), 1, '001')");
$stmt->bind_param("sssss", $member_id, $name, $email, $hashed, $phone);

if ($stmt->execute()) {
    // ✅ 註冊成功，直接登入
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
    unset($_SESSION['google_email']);
    header("Location: index.php");
    exit;
} else {
    echo "❌ 註冊失敗：" . $stmt->error;
}
?>

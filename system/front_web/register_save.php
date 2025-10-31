<?php
require 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 接收 POST 資料
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

// 驗證密碼一致性
if ($password !== $confirm) {
    die("❌ 兩次輸入的密碼不一致，請<a href='register.php'>返回重填</a>。");
}

// 檢查 Email 是否已存在
$stmt = $db->prepare("SELECT member_id FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("❌ 此 Email 已註冊過，請<a href='register.php'>使用其他信箱</a>。");
}
$stmt->close();

// 自動產生下一個 member_id（固定3位數）
$result = $db->query("SELECT MAX(member_id) AS last_id FROM members");
$row = $result->fetch_assoc();
$last_id = $row['last_id'];

if ($last_id) {
    $next_num = intval($last_id) + 1;
    $member_id = str_pad($next_num, 3, "0", STR_PAD_LEFT); // 001, 002...
} else {
    $member_id = "001";
}

// 加密密碼
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 寫入 members 資料表
$stmt = $db->prepare("INSERT INTO members (member_id, name, email, password, phone, registration_date, level, admin_id) VALUES (?, ?, ?, ?, ?, NOW(), 1, '001')");
$stmt->bind_param("sssss", $member_id, $name, $email, $hashed, $phone);

if ($stmt->execute()) {
    echo "✅ 註冊成功，請<a href='user.php'>點此登入</a>。";
} else {
    echo "❌ 註冊失敗：" . $stmt->error;
}
?>

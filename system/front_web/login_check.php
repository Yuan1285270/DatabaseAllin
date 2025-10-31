<?php
session_start();
require 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $db->prepare("SELECT member_id, password, name FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($member_id, $hashed_password, $name);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['member_id'] = $member_id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        echo "✅ 登入成功，<a href='index.php'>回首頁</a>";
        exit;
    }
}

echo "❌ 電子郵件或密碼錯誤，請<a href='user.php'>重新登入</a>";
?>

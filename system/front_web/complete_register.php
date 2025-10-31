<?php
session_start();
require 'db.php';
$db = $conn;

if (!isset($_SESSION['pending_email'])) {
    header("Location: user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['pending_email'];
    $google_id = $_SESSION['pending_google_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $member_id = 'google_' . substr(md5($google_id), 0, 10);

    $stmt = $db->prepare("INSERT INTO members (member_id, name, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $member_id, $name, $email, $phone);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $member_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        unset($_SESSION['pending_email'], $_SESSION['pending_google_id']);
        header("Location: front_web/index.php?welcome=1");
        exit;
    } else {
        echo "❌ 註冊失敗：" . $stmt->error;
    }
}
?>

<!-- HTML 表單 -->
<form method="POST">
  <h2>請補齊資訊完成註冊</h2>
  <input type="text" name="name" placeholder="姓名" required><br>
  <input type="text" name="phone" placeholder="手機號碼" required><br>
  <button type="submit">完成註冊</button>
</form>

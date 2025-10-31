<?php
session_start();
require_once 'db.php';
$db = $conn;

// 未登入就導回登入頁
if (!isset($_SESSION['email'])) {
  header("Location: user.php");
  exit;
}

// 驗證商品 ID
if (!isset($_POST['product_id'])) {
  die("❌ 未提供商品 ID");
}

$product_id = $_POST['product_id'];
$user_email = $_SESSION['email'];

// 查詢使用者會員 ID（member_id）
$stmt = $db->prepare("SELECT member_id FROM members WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  die("❌ 找不到使用者資料");
}

$member_id = $user['member_id'];

// 加入購物車（可檢查是否重複）
$stmt = $db->prepare("INSERT INTO cart (member_id, product_id, quantity) VALUES (?, ?, 1)");
$stmt->bind_param("ss", $member_id, $product_id);

if ($stmt->execute()) {
  header("Location: cart.php");
  exit;
} else {
  echo "❌ 加入購物車失敗：" . $stmt->error;
}
?>

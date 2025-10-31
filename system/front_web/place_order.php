<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db.php';
$db = $conn;

// 驗證登入
if (!isset($_SESSION['email'])) {
  header("Location: user.php");
  exit;
}

// 取得會員 ID
$email = $_SESSION['email'];
$stmt = $db->prepare("SELECT member_id FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$member_id = $user['member_id'] ?? null;
if (!$member_id) die("找不到會員資訊");

// 接收表單資料
$address = $_POST['shipment_address'] ?? '';
$note = $_POST['problem_description'] ?? '';
if (!$address) die("請輸入地址");

// 撈取購物車資料
$stmt = $db->prepare("SELECT product_id, SUM(quantity) AS quantity FROM cart WHERE member_id = ? GROUP BY product_id");
$stmt->bind_param("s", $member_id);
$stmt->execute();
$cart = $stmt->get_result();

if ($cart->num_rows === 0) {
  die("購物車為空，無法下單");
}

// 計算總金額
$total = 0;
$cart_items = [];
while ($row = $cart->fetch_assoc()) {
  $cart_items[] = $row;
  $pid = $row['product_id'];
  $qty = $row['quantity'];
  // 查價格
  $stmt2 = $db->prepare("SELECT price, storage FROM products WHERE product_id = ?");
  $stmt2->bind_param("s", $pid);
  $stmt2->execute();
  $product = $stmt2->get_result()->fetch_assoc();
  if ($product['storage'] < $qty) {
    die("商品庫存不足：$pid");
  }
  $total += $product['price'] * $qty;
}

// 產生 order_id
$stmt = $db->query("SELECT MAX(CAST(order_id AS UNSIGNED)) AS max_id FROM orders");
$row = $stmt->fetch_assoc();
$next_order_id = str_pad((int)$row['max_id'] + 1, 3, '0', STR_PAD_LEFT);

// 建立訂單
$stmt = $db->prepare("INSERT INTO orders (order_id, submission_date, problem_description, status, shipment_address, total, member_id)
                      VALUES (?, NOW(), ?, '處理中', ?, ?, ?)");
$stmt->bind_param("sssds", $next_order_id, $note, $address, $total, $member_id);
$stmt->execute();

// 寫入每筆商品到 orders_product 並扣庫存
foreach ($cart_items as $item) {
  $pid = $item['product_id'];
  $qty = $item['quantity'];
  // orders_product
  $stmt = $db->prepare("INSERT INTO orders_product (order_id, product_id, quantity) VALUES (?, ?, ?)");
  $stmt->bind_param("ssi", $next_order_id, $pid, $qty);
  $stmt->execute();
  // 更新庫存
  $stmt = $db->prepare("UPDATE products SET storage = storage - ? WHERE product_id = ?");
  $stmt->bind_param("is", $qty, $pid);
  $stmt->execute();
}

// 清空購物車
$stmt = $db->prepare("DELETE FROM cart WHERE member_id = ?");
$stmt->bind_param("s", $member_id);
$stmt->execute();

// 成功導向
header("Location: order_success.php?order_id=$next_order_id");
exit;
?>

<?php
session_start();
require_once 'db.php';
$db = $conn;

// 確認登入
if (!isset($_SESSION['email'])) {
  header("Location: user.php");
  exit;
}

if (!isset($_GET['order_id'])) {
  die("❌ 未提供訂單編號");
}

$order_id = $_GET['order_id'];

$stmt = $db->prepare("SELECT o.submission_date, o.shipment_address, o.total, m.name 
                      FROM orders o 
                      JOIN members m ON o.member_id = m.member_id 
                      WHERE o.order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
  die("❌ 找不到訂單資料");
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>下單成功 | 歐印商店</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background: #f2f7f5;
      font-family: "Segoe UI", "Microsoft JhengHei", sans-serif;
    }
    .success-box {
      max-width: 600px;
      margin: 80px auto;
      background: white;
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      text-align: center;
    }
    .success-box .icon {
      font-size: 48px;
      color: #4CAF50;
    }
    .success-box h2 {
      font-size: 26px;
      color: #2e7d32;
      margin: 20px 0;
    }
    .success-box p {
      font-size: 16px;
      margin: 8px 0;
      color: #444;
    }
    .success-box .order-info {
      margin-top: 24px;
      text-align: left;
      padding: 0 10px;
    }
    .success-box .btn-group {
      margin-top: 30px;
    }
    .success-box .btn-group a {
      display: inline-block;
      margin: 5px 12px;
      padding: 10px 20px;
      border-radius: 6px;
      font-size: 15px;
      text-decoration: none;
      transition: background 0.3s;
    }
    .btn-home {
      background-color: #4CAF50;
      color: white;
    }
    .btn-home:hover {
      background-color: #43a047;
    }
    .btn-shop {
      background-color: #1976d2;
      color: white;
    }
    .btn-shop:hover {
      background-color: #1565c0;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="success-box">
    <div class="icon">✅</div>
    <h2>感謝您的訂購！</h2>
    <p>您的訂單已成功送出，我們將盡快為您處理出貨。</p>

    <div class="order-info">
      <p><strong>訂單編號：</strong> <?= htmlspecialchars($order_id) ?></p>
      <p><strong>訂購人：</strong> <?= htmlspecialchars($order['name']) ?></p>
      <p><strong>下單時間：</strong> <?= htmlspecialchars($order['submission_date']) ?></p>
      <p><strong>寄送地址：</strong> <?= htmlspecialchars($order['shipment_address']) ?></p>
      <p><strong>總金額：</strong> <?= htmlspecialchars($order['total']) ?> 元</p>
    </div>

    <div class="btn-group">
      <a href="index.php" class="btn-home">🏠 回首頁</a>
      <a href="product.php" class="btn-shop">🛍 繼續購物</a>
    </div>
  </main>

  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>

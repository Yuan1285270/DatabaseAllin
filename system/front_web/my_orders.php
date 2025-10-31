<?php
session_start();
require_once 'db.php';
$db = $conn;

if (!isset($_SESSION['email'])) {
  header("Location: user.php");
  exit;
}

// 取得會員的所有訂單
$stmt = $db->prepare("
  SELECT o.order_id, o.submission_date, o.problem_description, o.status, o.shipment_address, o.total
  FROM orders o
  JOIN members m ON o.member_id = m.member_id
  WHERE m.email = ?
  ORDER BY o.submission_date DESC
");
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>我的訂單資訊｜歐印</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .order-container {
      max-width: 900px;
      margin: 60px auto;
      padding: 20px;
      background: #f9f9f9;
      border-radius: 12px;
    }
    .order-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    table.orders {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    table.orders th, table.orders td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }
    table.orders th {
      background: #e0e0e0;
    }
    .product-list {
      margin-left: 20px;
      font-size: 14px;
      color: #333;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="order-container">
    <h2>📋 我的訂單資訊</h2>
    <?php if ($orders->num_rows > 0): ?>
      <?php while ($row = $orders->fetch_assoc()): ?>
        <table class="orders">
          <tr><th>訂單編號</th><td><?= htmlspecialchars($row['order_id']) ?></td></tr>
          <tr><th>下單時間</th><td><?= htmlspecialchars($row['submission_date']) ?></td></tr>
          <tr><th>寄送地址</th><td><?= htmlspecialchars($row['shipment_address']) ?></td></tr>
          <tr><th>備註</th><td><?= htmlspecialchars($row['problem_description']) ?></td></tr>
          <tr><th>總金額</th><td><?= $row['total'] ?> 元</td></tr>
          <tr><th>狀態</th><td><?= htmlspecialchars($row['status']) ?></td></tr>

          <!-- 🔽 加入訂單商品清單 -->
          <tr>
            <th>訂購商品</th>
            <td>
              <ul class="product-list">
                <?php
                $order_id = $row['order_id'];
                $stmt2 = $db->prepare("
                  SELECT p.name, op.quantity 
                  FROM orders_product op
                  JOIN products p ON op.product_id = p.product_id
                  WHERE op.order_id = ?
                ");
                $stmt2->bind_param("s", $order_id);
                $stmt2->execute();
                $products = $stmt2->get_result();
                while ($prod = $products->fetch_assoc()):
                ?>
                  <li><?= htmlspecialchars($prod['name']) ?>（數量：<?= $prod['quantity'] ?>）</li>
                <?php endwhile; ?>
              </ul>
            </td>
          </tr>
        </table>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;">目前沒有任何訂單紀錄。</p>
    <?php endif; ?>
  </main>

  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>

<?php
session_start();
require_once 'db.php';
$db = $conn;

// 檢查是否登入
if (!isset($_SESSION['email'])) {
  header("Location: user.php");
  exit;
}

// 查詢該會員的訂單資訊
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
    }
    table.orders th, table.orders td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }
    table.orders th {
      background: #e0e0e0;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="order-container">
    <h2>📋 我的訂單資訊</h2>
    <?php if ($orders->num_rows > 0): ?>
      <table class="orders">
        <thead>
          <tr>
            <th>訂單編號</th>
            <th>下單時間</th>
            <th>總金額</th>
            <th>狀態</th>
            <th>寄送地址</th>
            <th>備註</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['order_id']) ?></td>
              <td><?= htmlspecialchars($row['submission_date']) ?></td>
              <td><?= $row['total'] ?> 元</td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td><?= htmlspecialchars($row['shipment_address']) ?></td>
              <td><?= htmlspecialchars($row['problem_description']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="text-align:center;">目前沒有任何訂單紀錄。</p>
    <?php endif; ?>
  </main>

  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>

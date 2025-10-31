<?php
include 'navbar.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$order_id = $_GET['order_id'] ?? '';
if (empty($order_id)) {
    die('缺少訂單 ID');
}

$order_sql = 'SELECT o.*, m.name AS member_name
              FROM orders o
              JOIN members m ON o.member_id = m.member_id
              WHERE o.order_id = ?';
$stmt = $conn->prepare($order_sql);
$stmt->bind_param('s', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$order) {
    die('找不到此訂單');
}

$items_sql = 'SELECT op.*, p.name, p.price
              FROM orders_product op
              JOIN products p ON op.product_id = p.product_id
              WHERE op.order_id = ?';
$stmt = $conn->prepare($items_sql);
$stmt->bind_param('s', $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>訂單明細</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
    }
    body {
      padding-top: 100px; /* 推開 navbar */
      font-family: "Microsoft JhengHei", sans-serif;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <h2 class="mb-4">訂單明細 #<?= htmlspecialchars($order['order_id']) ?></h2>

    <p><strong>會員：</strong><?= htmlspecialchars($order['member_name']) ?></p>
    <p><strong>訂單時間：</strong><?= htmlspecialchars($order['submission_date']) ?></p>
    <p><strong>總金額：</strong>$<?= number_format($order['total'], 2) ?></p>
    <p><strong>狀態：</strong><?= htmlspecialchars($order['status']) ?></p>
    <p><strong>收貨地址：</strong><?= htmlspecialchars($order['shipment_address']) ?></p>
    <p><strong>備註：</strong><?= htmlspecialchars($order['problem_description']) ?></p>

    <h4 class="mt-4">商品列表</h4>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>商品名稱</th><th>單價</th><th>數量</th><th>小計</th>
        </tr>
      </thead>
      <tbody>
        <?php $total_check = 0; while ($item = $items_result->fetch_assoc()):
          $subtotal = $item['price'] * $item['quantity'];
          $total_check += $subtotal;
        ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($subtotal, 2) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <p class="fw-bold text-end">訂單總額確認：$<?= number_format($total_check, 2) ?></p>

    <a href="orders_manage.php" class="btn btn-secondary">返回訂單總覽</a>
    <a href="order_edit.php?order_id=<?= urlencode($order['order_id']) ?>" class="btn btn-primary me-2">編輯訂單</a>
    <a href="order_delete.php?order_id=<?= urlencode($order['order_id']) ?>" class="btn btn-danger ms-2"
       onclick="return confirm('確定要刪除這筆訂單嗎？');">刪除訂單</a>
  </div>
</body>
</html>

<?php
// 檔名：order_create.php

// ---------- 基本設定 ----------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'db.php';   // $conn

// ---------- 表單送出 ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 取得並驗證資料
    $order_id         = trim($_POST['order_id'] ?? '');
    $member_id        = trim($_POST['member_id'] ?? '');
    $status           = trim($_POST['status'] ?? '處理中');
    $shipment_address = trim($_POST['shipment_address'] ?? '');
    $problem_desc     = trim($_POST['problem_description'] ?? '');
    $product_ids      = $_POST['product_id'] ?? [];
    $quantities       = $_POST['quantity']   ?? [];

    if (!$order_id || !$member_id || !$shipment_address) {
        exit('❌ 訂單編號 / 會員 / 地址 為必填');
    }
    if (count($product_ids) === 0 || count($quantities) === 0) {
        exit('❌ 請至少選一項商品並輸入數量');
    }

    // 計算總金額
    $total = 0;
    $products = [];  // [ [pid, qty] ]
    foreach ($product_ids as $i => $pid) {
        $qty = max(1, (int)$quantities[$i]);

        $stmt = $conn->prepare('SELECT price FROM products WHERE product_id = ?');
        $stmt->bind_param('s', $pid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) exit("❌ 找不到產品 $pid");

        $price  = (float)$row['price'];
        $total += $price * $qty;
        $products[] = [$pid, $qty];
    }

    try {
        $conn->begin_transaction();

        // 寫入 orders
        $stmt = $conn->prepare(
            'INSERT INTO orders
             (order_id, submission_date, problem_description,
              status, shipment_address, total, member_id)
             VALUES (?, NOW(), ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'ssssds',
            $order_id,
            $problem_desc,
            $status,
            $shipment_address,
            $total,
            $member_id
        );
        $stmt->execute();

        // 寫入 orders_product
        $stmt = $conn->prepare(
            'INSERT INTO orders_product (order_id, product_id, quantity)
             VALUES (?, ?, ?)'
        );
        foreach ($products as [$pid, $qty]) {
            $stmt->bind_param('ssi', $order_id, $pid, $qty);
            $stmt->execute();
        }

        $conn->commit();

        // 導向明細頁（帶上 order_id）
        header('Location: order_detail.php?order_id=' . urlencode($order_id));
        exit;

    } catch (Throwable $e) {
        $conn->rollback();
        exit('❌ 交易失敗：' . $e->getMessage());
    }
}

// ---------- 讀取下拉資料 ----------
$members      = $conn->query('SELECT member_id, name FROM members');
$product_list = $conn->query('SELECT product_id, name, storage FROM products');

// 計算每個商品的剩餘庫存：storage - 已訂購總數
$remaining = [];
while ($prod = $product_list->fetch_assoc()) {
    $pid = $prod['product_id'];
    $storage = (int)$prod['storage'];

    $stmt = $conn->prepare('SELECT SUM(quantity) AS sold FROM orders_product WHERE product_id = ?');
    $stmt->bind_param('s', $pid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $sold = (int)($row['sold'] ?? 0);

    $remaining[$pid] = max(0, $storage - $sold);
    $stmt->close();
}
// 重設 $product_list 的指標，之後 include product_row.php 時才可重新遍歷
$product_list->data_seek(0);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>新增訂單</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* 因為 navbar 使用 fixed-top，需要預留上方空間 */
    body {
      padding-top: 70px; /* 視 navbar 實際高度調整 */
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="container p-4">
    <h2 class="mb-4">新增訂單</h2>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">訂單編號</label>
        <input type="text" name="order_id" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">會員</label>
        <select name="member_id" class="form-select" required>
          <?php while ($m = $members->fetch_assoc()): ?>
            <option value="<?= $m['member_id'] ?>">
              <?= $m['member_id'] ?> - <?= htmlspecialchars($m['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">收貨地址</label>
        <input type="text" name="shipment_address" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">訂單狀態</label>
        <input type="text" name="status" class="form-control" value="處理中">
      </div>

      <div class="mb-3">
        <label class="form-label">備註</label>
        <textarea name="problem_description" class="form-control"></textarea>
      </div>

      <h5 class="mt-4">商品內容</h5>
      <div id="products">
        <?php include 'product_row.php'; ?>
      </div>

      <template id="tmpl-row"><?php include 'product_row.php'; ?></template>

      <button type="button" id="btn-add" class="btn btn-secondary my-3">新增商品</button>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">建立訂單</button>
      </div>
    </form>
  </div>

  <script>
  // 當選擇商品或動態新增列時，更新剩餘庫存顯示
  function updateRemaining(elem) {
    const row = elem.closest('.product-row');
    const selectElem = row.querySelector('.product-select');
    const display = row.querySelector('.remaining-display');
    const remaining = selectElem.selectedOptions[0]?.dataset.remaining ?? '--';
    display.textContent = remaining;
  }

  // 初始化已存在列
  document.querySelectorAll('.product-select').forEach(sel => {
    sel.addEventListener('change', () => updateRemaining(sel));
    updateRemaining(sel);
  });

  // 新增列時加事件、更新顯示
  document.getElementById('btn-add').addEventListener('click', () => {
    const tmpl = document.getElementById('tmpl-row').content.cloneNode(true);
    document.getElementById('products').appendChild(tmpl);

    // 新增後立即綁定事件並更新顯示
    const rows = document.querySelectorAll('.product-row');
    const newRow = rows[rows.length - 1];
    const select = newRow.querySelector('.product-select');
    select.addEventListener('change', () => updateRemaining(select));
    updateRemaining(select);
  });

  // 移除列
  document.getElementById('products').addEventListener('click', e => {
    if (e.target.classList.contains('btn-remove')) {
      e.target.closest('.product-row').remove();
    }
  });
  </script>
</body>
</html>

<?php
include 'navbar.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'db.php';

$order_id = $_GET['order_id'] ?? '';
if (!$order_id) die('缺少訂單 ID');

$order_sql = 'SELECT * FROM orders WHERE order_id = ?';
$stmt = $conn->prepare($order_sql);
$stmt->bind_param('s', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$order) die('找不到此訂單');

$item_sql = 'SELECT op.product_id, op.quantity, p.name, p.price
             FROM orders_product op
             JOIN products p ON op.product_id = p.product_id
             WHERE op.order_id = ?';
$stmt = $conn->prepare($item_sql);
$stmt->bind_param('s', $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status           = trim($_POST['status'] ?? '處理中');
    $shipment_address = trim($_POST['shipment_address'] ?? '');
    $problem_desc     = trim($_POST['problem_description'] ?? '');
    $product_ids      = $_POST['product_id'] ?? [];
    $quantities       = $_POST['quantity']   ?? [];

    if (!$shipment_address) exit('❌ 收貨地址必填');
    if (count($product_ids) === 0) exit('❌ 至少保留一項商品');

    $total = 0;
    $products = [];
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

        $stmt = $conn->prepare(
            'UPDATE orders
             SET problem_description = ?,
                 status             = ?,
                 shipment_address   = ?,
                 total              = ?
             WHERE order_id = ?'
        );
        $stmt->bind_param('ssdds',
            $problem_desc, $status,
            $shipment_address, $total,
            $order_id
        );
        $stmt->execute();

        $conn->query('DELETE FROM orders_product WHERE order_id = "' . $conn->real_escape_string($order_id) . '"');

        $stmt = $conn->prepare(
            'INSERT INTO orders_product (order_id, product_id, quantity)
             VALUES (?, ?, ?)'
        );
        foreach ($products as [$pid, $qty]) {
            $stmt->bind_param('ssi', $order_id, $pid, $qty);
            $stmt->execute();
        }

        $conn->commit();

        header('Location: order_detail.php?order_id=' . urlencode($order_id));
        exit;

    } catch (Throwable $e) {
        $conn->rollback();
        exit('❌ 更新失敗：' . $e->getMessage());
    }
}

$product_list = $conn->query('SELECT product_id, name FROM products');
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>編輯訂單 #<?= htmlspecialchars($order_id) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
    }
    body {
      padding-top: 100px;
      font-family: "Microsoft JhengHei", sans-serif;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <h2 class="mb-4">編輯訂單 #<?= htmlspecialchars($order_id) ?></h2>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">訂單編號</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($order_id) ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">會員</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($order['member_id']) ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">收貨地址</label>
        <input type="text" name="shipment_address" class="form-control" value="<?= htmlspecialchars($order['shipment_address']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">訂單狀態</label>
        <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($order['status']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">備註</label>
        <textarea name="problem_description" class="form-control"><?= htmlspecialchars($order['problem_description']) ?></textarea>
      </div>

      <h5 class="mt-4">商品內容</h5>
      <div id="products">
        <?php foreach ($items as $row): ?>
          <?php
            $pid = $row['product_id'];
            $qty = $row['quantity'];
          ?>
          <div class="row align-items-end product-row mb-2">
            <div class="col-md-6">
              <label class="form-label">商品</label>
              <select name="product_id[]" class="form-select" required>
                <?php
                mysqli_data_seek($product_list, 0);
                while ($p = $product_list->fetch_assoc()):
                  $sel = $p['product_id'] == $pid ? 'selected' : '';
                ?>
                  <option value="<?= $p['product_id'] ?>" <?= $sel ?>>
                    <?= $p['product_id'] ?> - <?= htmlspecialchars($p['name']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">數量</label>
              <input type="number" name="quantity[]" class="form-control" value="<?= $qty ?>" min="1" required>
            </div>
            <div class="col-md-3 text-end">
              <button type="button" class="btn btn-danger btn-remove mt-4">移除</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <template id="tmpl-row">
        <div class="row align-items-end product-row mb-2">
          <div class="col-md-6">
            <label class="form-label">商品</label>
            <select name="product_id[]" class="form-select" required>
              <?php
              mysqli_data_seek($product_list, 0);
              while ($p = $product_list->fetch_assoc()): ?>
                <option value="<?= $p['product_id'] ?>">
                  <?= $p['product_id'] ?> - <?= htmlspecialchars($p['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">數量</label>
            <input type="number" name="quantity[]" class="form-control" value="1" min="1" required>
          </div>
          <div class="col-md-3 text-end">
            <button type="button" class="btn btn-danger btn-remove mt-4">移除</button>
          </div>
        </div>
      </template>

      <button type="button" id="btn-add" class="btn btn-secondary my-3">新增商品</button>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">儲存變更</button>
        <a href="order_detail.php?order_id=<?= urlencode($order_id) ?>" class="btn btn-outline-secondary ms-2">取消</a>
      </div>
    </form>
  </div>

  <script>
    document.getElementById('btn-add').addEventListener('click', () => {
      const tmpl = document.getElementById('tmpl-row').content.cloneNode(true);
      document.getElementById('products').appendChild(tmpl);
    });
    document.getElementById('products').addEventListener('click', e => {
      if (e.target.classList.contains('btn-remove')) {
        e.target.closest('.product-row').remove();
      }
    });
  </script>
</body>
</html>

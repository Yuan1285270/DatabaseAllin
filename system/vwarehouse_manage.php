<?php

include 'navbar.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'db.php';

$msg = '';
$admin_id = '001';

/* ---------- 儲存 / 更新倉庫 ---------- */
if (isset($_POST['save'])) {
    $errors = [];
    if (empty($_POST['vwarehouse_id']) && empty($_POST['edit_id'])) $errors[] = '倉庫 ID 不能空白';
    if (empty($_POST['name']))     $errors[] = '倉庫名稱不能空白';
    if (empty($_POST['location'])) $errors[] = '地點不能空白';

    if (!$errors) {
        if (!empty($_POST['edit_id'])) {
            $stmt = $conn->prepare(
                'UPDATE vwarehouse SET name=?, location=?, admin_id=? WHERE vwarehouse_id=?'
            );
            $stmt->bind_param('ssss',
                $_POST['name'], $_POST['location'], $admin_id, $_POST['edit_id']
            );
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO vwarehouse (vwarehouse_id, name, location, admin_id)
                 VALUES (?,?,?,?)'
            );
            $stmt->bind_param('ssss',
                $_POST['vwarehouse_id'], $_POST['name'], $_POST['location'], $admin_id
            );
        }
        $stmt->execute();
        $stmt->close();
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }
}

/* ---------- 刪除倉庫 ---------- */
if (isset($_POST['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM vwarehouse WHERE vwarehouse_id = ?');
    $stmt->bind_param('s', $_POST['delete_id']);
    $stmt->execute();
    $stmt->close();
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

/* ---------- 新增 / 更新 庫存 ---------- */
if (isset($_POST['add_item'])) {
    $vwid = $_POST['vwarehouse'];
    $pid  = $_POST['product_id'];
    $qty  = (int)$_POST['quantity'];

    $stmt = $conn->prepare(
        'REPLACE INTO vwarehouse_products (vwarehouse_id, product_id, quantity)
         VALUES (?,?,?)'
    );
    $stmt->bind_param('ssi', $vwid, $pid, $qty);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare(
        'UPDATE products
         SET storage = (SELECT COALESCE(SUM(quantity),0)
                        FROM vwarehouse_products WHERE product_id = ?),
             status  = IF((SELECT COALESCE(SUM(quantity),0)
                           FROM vwarehouse_products WHERE product_id = ?) = 0,
                          "沒貨了", status)
         WHERE product_id = ?'
    );
    $stmt->bind_param('sss', $pid, $pid, $pid);
    $stmt->execute();
    $stmt->close();

    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

/* ---------- 刪除 庫存 ---------- */
if (isset($_POST['delete_item'])) {
    $vwid = $_POST['vw'];
    $pid  = $_POST['prod'];

    $stmt = $conn->prepare(
        'DELETE FROM vwarehouse_products WHERE vwarehouse_id = ? AND product_id = ?'
    );
    $stmt->bind_param('ss', $vwid, $pid);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare(
        'UPDATE products
         SET storage = (SELECT COALESCE(SUM(quantity),0)
                        FROM vwarehouse_products WHERE product_id = ?),
             status  = IF((SELECT COALESCE(SUM(quantity),0)
                           FROM vwarehouse_products WHERE product_id = ?) = 0,
                          "沒貨了", status)
         WHERE product_id = ?'
    );
    $stmt->bind_param('sss', $pid, $pid, $pid);
    $stmt->execute();
    $stmt->close();

    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

/* ---------- 查詢倉庫 ---------- */
$search = $_GET['search'] ?? '';
$sql = 'SELECT * FROM vwarehouse';
if ($search) $sql .= " WHERE name LIKE '%$search%' OR location LIKE '%$search%'";
$sql .= ' ORDER BY name';
$result   = $conn->query($sql);
$products = $conn->query('SELECT product_id, name FROM products');

$edit      = isset($_GET['edit']);
$edit_data = $edit
  ? $conn->query("SELECT * FROM vwarehouse WHERE vwarehouse_id = '".$conn->real_escape_string($_GET['edit'])."'")->fetch_assoc()
  : [];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>虛擬倉庫管理</title>
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
    <h2 class="mb-4">虛擬倉庫管理</h2>

    <!-- 搜尋 -->
    <form method="get" class="mb-3">
      <input type="text" name="search" class="form-control w-50 d-inline"
             placeholder="搜尋倉庫名稱或地點" value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-secondary">搜尋</button>
      <a href="vwarehouse_manage.php" class="btn btn-outline-secondary">清除</a>
    </form>

    <!-- 新增 / 編輯倉庫表單 -->
    <form method="post" class="row g-3 mb-5">
      <input type="hidden" name="edit_id" value="<?= $edit ? $edit_data['vwarehouse_id'] : '' ?>">
      <div class="col-md-4">
        <label class="form-label">倉庫 ID</label>
        <input type="text" name="vwarehouse_id" class="form-control"
               value="<?= $edit ? $edit_data['vwarehouse_id'] : '' ?>"
               <?= $edit ? 'readonly' : 'required' ?>>
      </div>
      <div class="col-md-4">
        <label class="form-label">倉庫名稱</label>
        <input type="text" name="name" class="form-control" required
               value="<?= $edit ? $edit_data['name'] : '' ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">地點</label>
        <input type="text" name="location" class="form-control" required
               value="<?= $edit ? $edit_data['location'] : '' ?>">
      </div>
      <div class="col-12">
        <button name="save" class="btn btn-primary"><?= $edit ? '更新倉庫' : '新增倉庫' ?></button>
        <?php if ($edit): ?><a href="vwarehouse_manage.php" class="btn btn-secondary">取消</a><?php endif; ?>
      </div>
    </form>

    <?php while ($vw = $result->fetch_assoc()): ?>
      <div class="card mb-4">
        <div class="card-header">
          <strong><?= $vw['vwarehouse_id'].' - '.$vw['name'] ?></strong>（<?= $vw['location'] ?>）
          <div class="float-end">
            <a href="?edit=<?= $vw['vwarehouse_id'] ?>" class="btn btn-sm btn-warning">編輯</a>
            <form method="post" style="display:inline" onsubmit="return confirm('確定刪除倉庫？');">
              <input type="hidden" name="delete_id" value="<?= $vw['vwarehouse_id'] ?>">
              <button class="btn btn-sm btn-danger">刪除</button>
            </form>
          </div>
        </div>

        <div class="card-body">
          <h6 class="mb-3">商品庫存：</h6>

          <!-- 新增 / 更新商品數量 -->
          <form method="post" class="row g-2 mb-2">
            <input type="hidden" name="vwarehouse" value="<?= $vw['vwarehouse_id'] ?>">
            <div class="col-md-4">
              <select name="product_id" class="form-select" required>
                <option value="">選擇商品</option>
                <?php $products->data_seek(0);
                while ($p = $products->fetch_assoc()): ?>
                  <option value="<?= $p['product_id'] ?>">
                    <?= $p['product_id'].' - '.$p['name'] ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-3">
              <input type="number" name="quantity" min="0" class="form-control"
                     placeholder="數量" required>
            </div>
            <div class="col-md-2">
              <button name="add_item" class="btn btn-success">更新</button>
            </div>
          </form>

          <!-- 庫存列表 -->
          <table class="table table-sm">
            <thead class="table-light">
              <tr><th>商品</th><th>數量</th><th>操作</th></tr>
            </thead>
            <tbody>
            <?php
              $items = $conn->query(
                'SELECT vp.*, p.name
                 FROM vwarehouse_products vp
                 JOIN products p ON vp.product_id = p.product_id
                 WHERE vp.vwarehouse_id = "'.$conn->real_escape_string($vw['vwarehouse_id']).'"'
              );
              while ($row = $items->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['product_id']).' - '.htmlspecialchars($row['name']) ?></td>
                <td><?= (int)$row['quantity'] ?></td>
                <td>
                  <form method="post" style="display:inline"
                        onsubmit="return confirm('確定刪除此庫存？');">
                    <input type="hidden" name="delete_item" value="1">
                    <input type="hidden" name="vw" value="<?= $vw['vwarehouse_id'] ?>">
                    <input type="hidden" name="prod" value="<?= $row['product_id'] ?>">
                    <button class="btn btn-sm btn-danger">刪除</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>

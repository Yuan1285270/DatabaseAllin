<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'db.php';

$admin_id = '001';

// 圖片上傳處理（新增）
$image_upload_message = '';
if (isset($_POST['upload_image'])) {
  if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '/home/D1285270/public_html/images/upload/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0755, true);
    }

    $filename = uniqid() . "_" . basename($_FILES['image_file']['name']);
    $target_path = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
      $image_url = "/~D1285270/images/upload/$filename";
      $image_upload_message = "
        <div class='alert alert-success'>
          圖片上傳成功！<br>
          圖片網址：<a href='$image_url' target='_blank'>$image_url</a>
          <script>
            window.addEventListener('DOMContentLoaded', function() {
              const input = document.querySelector('input[name=\"image\"]');
              if (input) {
                input.value = '$image_url';
              }
              const preview = document.getElementById('image-preview');
              if (preview) {
                preview.innerHTML = '<img src=\"$image_url\" style=\"max-height:100px;\">';
              }
            });
          </script>
        </div>";
    } else {
      $image_upload_message = "<div class='alert alert-danger'>圖片上傳失敗，請檢查權限。</div>";
    }
  } else {
    $image_upload_message = "<div class='alert alert-warning'>請選擇圖片再上傳。</div>";
  }
}

$msg = '';
$action     = $_REQUEST['action']     ?? '';
$product_id = $_REQUEST['product_id'] ?? '';

if ($action === 'delete' && $product_id) {
    $stmt = $conn->prepare('DELETE FROM products WHERE product_id = ?');
    $stmt->bind_param('s', $product_id);
    $msg  = $stmt->execute()
            ? '✅ 已刪除產品 ' . htmlspecialchars($product_id)
            : '❌ 刪除失敗：' . $stmt->error;
    $stmt->close();
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','update'])) {
    $fields = ['product_id','name','description','price','image','class'];
    foreach ($fields as $f) $$f = trim($_POST[$f] ?? '');

    if (!$product_id || !$name) die('❌ 產品 ID 與名稱必填');

    if ($action === 'add') {
        $sql = 'INSERT INTO products ('.implode(',',$fields).',storage,admin_id)
                VALUES (?,?,?,?,?,?,0,"001")';
    } else {
        $sql = 'UPDATE products
                SET name=?, description=?, price=?, image=?, class=?
                WHERE product_id=?';
    }
    $stmt = $conn->prepare($sql);
    if ($action === 'add') {
        $stmt->bind_param('ssssss', $product_id, $name, $description, $price, $image, $class);
    } else {
        $stmt->bind_param('ssssss', $name, $description, $price, $image, $class, $product_id);
    }

    $msg = $stmt->execute()
           ? ($action === 'add' ? '✅ 新增成功！' : '✅ 更新成功！')
           : '❌ 執行失敗：' . $stmt->error;
    $stmt->close();
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($msg));
    exit;
}

$edit_data = null;
if ($action === 'edit' && $product_id) {
    $stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ?');
    $stmt->bind_param('s', $product_id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$q = $_GET['q'] ?? '';
if ($q) {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare('SELECT * FROM products WHERE product_id LIKE ? OR name LIKE ? ORDER BY product_id');
    $stmt->bind_param('ss', $like, $like);
} else {
    $stmt = $conn->prepare('SELECT * FROM products ORDER BY product_id');
}
$stmt->execute();
$list = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>產品管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body { margin: 0; padding: 0; }
    body { padding-top: 100px; font-family: "Microsoft JhengHei", sans-serif; }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <h2 class="mb-4">產品管理</h2>

    <?php if ($msg = ($_GET['msg'] ?? $msg)): ?>
      <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <!-- 圖片上傳功能 -->
    <?= $image_upload_message ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label">上傳產品圖片</label>
        <input type="file" name="image_file" class="form-control" required>
      </div>
      <div class="col-12">
        <button type="submit" name="upload_image" class="btn btn-success">上傳圖片</button>
      </div>
    </form>

    <!-- 搜尋表單 -->
    <form class="row g-2 mb-4" method="get">
      <div class="col-auto">
        <input type="text" name="q" class="form-control"
               placeholder="搜尋產品 ID 或名稱" value="<?= htmlspecialchars($q) ?>">
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-primary">搜尋</button>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary ms-1">全部</a>
      </div>
    </form>

    <?php $is_edit = ($action === 'edit' && $edit_data); ?>
    <div class="card mb-4">
      <div class="card-header"><?= $is_edit ? '編輯產品 #' . $edit_data['product_id'] : '新增產品' ?></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $is_edit ? 'update' : 'add' ?>">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">產品 ID</label>
              <input type="text" name="product_id" class="form-control"
                     value="<?= htmlspecialchars($edit_data['product_id'] ?? '') ?>"
                     <?= $is_edit ? 'readonly' : 'required' ?>>
            </div>
            <div class="col-md-4">
              <label class="form-label">名稱</label>
              <input type="text" name="name" class="form-control"
                     value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">價格</label>
              <input type="number" step="0.01" name="price" class="form-control"
                     value="<?= htmlspecialchars($edit_data['price'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">圖片網址</label>
              <input type="text" name="image" class="form-control"
                     value="<?= htmlspecialchars($edit_data['image'] ?? '') ?>">
              <div id="image-preview" class="mt-2">
                <?php if (!empty($edit_data['image'])): ?>
                  <img src="<?= htmlspecialchars($edit_data['image']) ?>" style="max-height: 100px;">
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">分類</label>
              <select name="class" class="form-select" required>
              <?php
              $class_options = ['行李箱', '旅遊周邊', '配件'];
              $selected = $edit_data['class'] ?? '';
              foreach ($class_options as $opt) {
              $sel = $opt === $selected ? 'selected' : '';
              echo "<option value=\"$opt\" $sel>$opt</option>";
              }  
              ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">描述</label>
              <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12 text-end">
              <button class="btn btn-primary"><?= $is_edit ? '儲存變更' : '新增產品' ?></button>
              <?php if ($is_edit): ?>
                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary ms-2">取消</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>

    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>名稱</th>
          <th>價格</th>
          <th>庫存狀態</th>
          <th>剩餘庫存</th>
          <th>管理</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $warehouse_stmt = $conn->prepare('SELECT COALESCE(SUM(quantity),0) AS total FROM vwarehouse_products WHERE product_id=?');
        $order_stmt = $conn->prepare('SELECT COALESCE(SUM(quantity),0) AS total FROM orders_product WHERE product_id=?');

        while ($row = $list->fetch_assoc()):
          $pid = $row['product_id'];

          $warehouse_stmt->bind_param('s', $pid);
          $warehouse_stmt->execute();
          $warehouse_stock = $warehouse_stmt->get_result()->fetch_assoc()['total'] ?? 0;

          $order_stmt->bind_param('s', $pid);
          $order_stmt->execute();
          $order_count = $order_stmt->get_result()->fetch_assoc()['total'] ?? 0;

          $remaining = $warehouse_stock - $order_count;
        ?>
          <tr>
            <td><?= htmlspecialchars($pid) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>$<?= number_format($row['price'], 2) ?></td>
            <td>
              <?= $remaining <= 0
                ? '<span class="text-danger">缺貨</span>'
                : '<span class="text-success">有貨</span>' ?>
            </td>
            <td><?= $remaining ?></td>
            <td>
              <a href="?action=edit&product_id=<?= urlencode($pid) ?>" class="btn btn-sm btn-primary">編輯</a>
              <a href="?action=delete&product_id=<?= urlencode($pid) ?>" class="btn btn-sm btn-danger ms-1"
                 onclick="return confirm('確定要刪除產品 <?= htmlspecialchars($pid) ?> 嗎？');">刪除</a>
            </td>
          </tr>
        <?php endwhile;
        $warehouse_stmt->close();
        $order_stmt->close();
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>

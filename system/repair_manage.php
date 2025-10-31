<?php
include 'navbar.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$edit = false;
$edit_data = [];

// 支援 POST 回來時保留編輯值
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id']) && $_POST['edit_id']) {
    $edit = true;
    $edit_id = $_POST['edit_id'];
    $edit_result = $conn->query("SELECT * FROM repair WHERE repair_id = '$edit_id'");
    $edit_data = $edit_result->fetch_assoc();
}

// 支援 GET 編輯
if (isset($_GET['edit'])) {
    $edit = true;
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM repair WHERE repair_id = '$edit_id'");
    $edit_data = $edit_result->fetch_assoc();
}

// 儲存或更新
if (isset($_POST['save'])) {
    $errors = [];
    if (empty($_POST['repair_id'])) $errors[] = "維修單號不能空白";
    if (empty($_POST['request_date'])) $errors[] = "申請日期不能空白";
    if (empty($_POST['member_id'])) $errors[] = "請選擇會員";
    if (empty($_POST['product_id'])) $errors[] = "請選擇商品";

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $e) echo "<li>$e</li>";
        echo "</ul></div>";
    } else {
        if (!empty($_POST['edit_id'])) {
            // ⭐ 若沒填保固到期日，就保留資料庫原值
            $warranty_due = ($_POST['warranty_due'] === '') ? $edit_data['warranty_due'] : $_POST['warranty_due'];

            $stmt = $conn->prepare("UPDATE repair SET request_date=?, problem_description=?, status=?, warranty_due=?, warranty_state=?, member_id=?, product_id=? WHERE repair_id=?");
            $stmt->bind_param("ssssssss",
                $_POST['request_date'],
                $_POST['problem_description'],
                $_POST['status'],
                $warranty_due,
                $_POST['warranty_state'],
                $_POST['member_id'],
                $_POST['product_id'],
                $_POST['edit_id']
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO repair (repair_id, request_date, problem_description, status, warranty_due, warranty_state, member_id, product_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss",
                $_POST['repair_id'],
                $_POST['request_date'],
                $_POST['problem_description'],
                $_POST['status'],
                $_POST['warranty_due'],
                $_POST['warranty_state'],
                $_POST['member_id'],
                $_POST['product_id']
            );
        }
        $stmt->execute();
        $stmt->close();

        $edit_id = $_POST['edit_id'] ?? '';
        $redirect = $edit_id !== '' ? "?edit=" . urlencode($edit_id) : "";
        header("Location: " . $_SERVER['PHP_SELF'] . $redirect);
        exit;
    }
}

// 刪除
if (isset($_POST['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM repair WHERE repair_id = ?");
    $stmt->bind_param("s", $_POST['delete_id']);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 查詢列表
$search = $_GET['search'] ?? '';
$sql = "SELECT r.*, m.name AS member_name, p.name AS product_name FROM repair r
        LEFT JOIN members m ON r.member_id = m.member_id
        LEFT JOIN products p ON r.product_id = p.product_id";
if (!empty($search)) {
    $sql .= " WHERE r.problem_description LIKE '%$search%' OR m.name LIKE '%$search%' OR p.name LIKE '%$search%'";
}
$sql .= " ORDER BY r.request_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>維修紀錄管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <style>
    html, body { margin: 0; padding: 0; }
    body { padding-top: 100px; font-family: "Microsoft JhengHei", sans-serif; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
  <h2 class="mb-4">維修紀錄管理</h2>

  <form method="GET" class="mb-3">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="搜尋問題、會員或商品" class="form-control w-50 d-inline">
    <button type="submit" class="btn btn-secondary">搜尋</button>
    <a href="repair_manage.php" class="btn btn-outline-secondary">清除</a>
  </form>

  <form method="POST" class="row g-3 mb-5">
    <input type="hidden" name="edit_id" value="<?= $edit_data['repair_id'] ?? '' ?>">
    <div class="col-md-4">
      <label class="form-label">維修單號</label>
      <input type="text" name="repair_id" class="form-control" value="<?= $edit_data['repair_id'] ?? '' ?>" <?= $edit ? 'readonly' : 'required' ?>>
    </div>
    <div class="col-md-4">
      <label class="form-label">申請日期</label>
      <input type="datetime-local" name="request_date" class="form-control" required
             value="<?= isset($edit_data['request_date']) ? str_replace(' ', 'T', $edit_data['request_date']) : '' ?>">
    </div>
    <div class="col-md-8">
      <label class="form-label">問題描述</label>
      <input type="text" name="problem_description" class="form-control" value="<?= $edit_data['problem_description'] ?? '' ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">狀態</label>
      <input type="text" name="status" class="form-control" value="<?= $edit_data['status'] ?? '' ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">保固到期</label>
      <input type="date" name="warranty_due" class="form-control"
             value="<?= isset($edit_data['warranty_due']) ? substr($edit_data['warranty_due'], 0, 10) : '' ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">保固狀態</label>
      <input type="text" name="warranty_state" class="form-control" value="<?= $edit_data['warranty_state'] ?? '' ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">會員</label>
      <select name="member_id" class="form-select select2" required>
        <option value="">請選擇會員</option>
        <?php
        $members = $conn->query("SELECT member_id, name FROM members");
        while ($m = $members->fetch_assoc()) {
            $selected = ($edit_data['member_id'] ?? '') == $m['member_id'] ? 'selected' : '';
            echo "<option value='{$m['member_id']}' $selected>{$m['member_id']} - {$m['name']}</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">商品</label>
      <select name="product_id" class="form-select select2" required>
        <option value="">請選擇商品</option>
        <?php
        $products = $conn->query("SELECT product_id, name FROM products");
        while ($p = $products->fetch_assoc()) {
            $selected = ($edit_data['product_id'] ?? '') == $p['product_id'] ? 'selected' : '';
            echo "<option value='{$p['product_id']}' $selected>{$p['product_id']} - {$p['name']}</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" name="save" class="btn btn-primary"><?= $edit ? '更新紀錄' : '新增紀錄' ?></button>
      <?php if ($edit): ?><a href="repair_manage.php" class="btn btn-secondary">取消</a><?php endif; ?>
    </div>
  </form>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>維修單號</th><th>會員</th><th>商品</th><th>問題</th><th>狀態</th><th>申請日</th><th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['repair_id']) ?></td>
          <td><?= htmlspecialchars($row['member_name']) ?></td>
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= htmlspecialchars($row['problem_description']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['request_date']) ?></td>
          <td>
            <a href="?edit=<?= $row['repair_id'] ?>" class="btn btn-sm btn-warning">編輯</a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('確定刪除？');">
              <input type="hidden" name="delete_id" value="<?= $row['repair_id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">刪除</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script>
  $(function() {
    $('.select2').select2();
  });
</script>
</body>
</html>

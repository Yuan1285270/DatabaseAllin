<?php

include 'navbar.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if (isset($_POST['save'])) {
    $errors = [];
    $admin_id = '001';

    if (empty($_POST['discount_id']) && empty($_POST['edit_id'])) $errors[] = "折扣 ID 不能空白";
    if (empty($_POST['code'])) $errors[] = "折扣碼不能空白";
    if (!is_numeric($_POST['discount']) || $_POST['discount'] < 1) $errors[] = "折扣金額必須是大於 0 的整數";
    if (empty($_POST['due'])) $errors[] = "請輸入到期日";

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $e) echo "<li>$e</li>";
        echo "</ul></div>";
    } else {
        if (!empty($_POST['edit_id'])) {
            $stmt = $conn->prepare("UPDATE discount_code SET code=?, discount=?, due=?, requirement=?, admin_id=? WHERE discount_id=?");
            $stmt->bind_param("sdssss",
                $_POST['code'],
                $_POST['discount'],
                $_POST['due'],
                $_POST['requirement'],
                $admin_id,
                $_POST['edit_id']
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO discount_code (discount_id, code, discount, due, requirement, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsss",
                $_POST['discount_id'],
                $_POST['code'],
                $_POST['discount'],
                $_POST['due'],
                $_POST['requirement'],
                $admin_id
            );
        }
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (isset($_POST['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM discount_code WHERE discount_id = ?");
    $stmt->bind_param("s", $_POST['delete_id']);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM discount_code";
if (!empty($search)) {
    $sql .= " WHERE code LIKE '%$search%' OR requirement LIKE '%$search%'";
}
$sql .= " ORDER BY due DESC";
$result = $conn->query($sql);

$edit = false;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit = true;
    $edit_result = $conn->query("SELECT * FROM discount_code WHERE discount_id = '$edit_id'");
    $edit_data = $edit_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>折扣碼管理</title>
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
    <h2 class="mb-4">折扣碼管理</h2>

    <form method="GET" class="mb-3">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="搜尋折扣碼或使用條件" class="form-control w-50 d-inline">
      <button type="submit" class="btn btn-secondary">搜尋</button>
      <a href="discount_manage.php" class="btn btn-outline-secondary">清除</a>
    </form>

    <form method="POST" class="row g-3 mb-5">
      <input type="hidden" name="edit_id" value="<?= $edit ? $edit_data['discount_id'] : '' ?>">
      <div class="col-md-4">
        <label class="form-label">折扣 ID</label>
        <input type="text" name="discount_id" class="form-control" value="<?= $edit ? $edit_data['discount_id'] : '' ?>" <?= $edit ? 'readonly' : 'required' ?>>
      </div>
      <div class="col-md-4">
        <label class="form-label">折扣碼 <span class='text-danger'>*</span></label>
        <input type="text" name="code" class="form-control" required value="<?= $edit ? $edit_data['code'] : '' ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">折扣金額 <span class='text-danger'>*</span></label>
        <input type="number" name="discount" min="1" class="form-control" required value="<?= $edit ? $edit_data['discount'] : '' ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">到期日 <span class='text-danger'>*</span></label>
        <input type="date" name="due" class="form-control" required value="<?= $edit ? $edit_data['due'] : '' ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">使用條件（可選）</label>
        <input type="text" name="requirement" class="form-control" value="<?= $edit ? $edit_data['requirement'] : '' ?>">
      </div>
      <div class="col-12">
        <button type="submit" name="save" class="btn btn-primary"><?= $edit ? '更新折扣' : '新增折扣' ?></button>
        <?php if ($edit): ?><a href="discount_manage.php" class="btn btn-secondary">取消</a><?php endif; ?>
      </div>
    </form>

    <table class="table table-bordered table-striped">
      <thead class="table-light">
        <tr>
          <th>折扣ID</th><th>折扣碼</th><th>折扣</th><th>到期</th><th>條件</th><th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['discount_id']) ?></td>
            <td><?= htmlspecialchars($row['code']) ?></td>
            <td><?= htmlspecialchars($row['discount']) ?></td>
            <td><?= htmlspecialchars($row['due']) ?></td>
            <td><?= htmlspecialchars($row['requirement']) ?></td>
            <td>
              <a href="?edit=<?= $row['discount_id'] ?>" class="btn btn-sm btn-warning">編輯</a>
              <form method="POST" style="display:inline;" onsubmit="return confirm('確定刪除？');">
                <input type="hidden" name="delete_id" value="<?= $row['discount_id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">刪除</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>